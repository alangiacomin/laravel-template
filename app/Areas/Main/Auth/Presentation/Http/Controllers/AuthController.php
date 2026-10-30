<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use App\Areas\Main\Auth\Application\Data\UserData;
use App\Areas\Main\Auth\Application\Jobs\RegisterUserJob;
use App\Areas\Main\Auth\Application\Jobs\ResendVerificationEmailJob;
use App\Areas\Main\Auth\Presentation\Http\Requests\LoginRequest;
use App\Areas\Main\Auth\Presentation\Http\Requests\RegisterRequest;
use App\Infrastructure\Http\Controllers\Controller;
use App\Infrastructure\Session\SessionPreserver;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Inertia\ResponseFactory;

class AuthController extends Controller
{
    private const int LOGIN_MAX_ATTEMPTS = 5;

    #[Middleware('guest')]
    public function loginView(): Response|ResponseFactory
    {
        return inertia('App/Login/Login');
    }

    #[Middleware('guest')]
    public function registerView(): Response|ResponseFactory
    {
        return inertia('App/Register/Register');
    }

    #[Middleware('auth')]
    #[Middleware('verified')]
    #[Middleware('not_banned')]
    public function userView(): Response|ResponseFactory
    {
        return inertia('App/User/User', [
            'user' => UserData::fromModel(Auth::user()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    #[Middleware('guest')]
    #[Middleware('throttle:register')]
    public function register(RegisterRequest $requestData): RedirectResponse
    {
        $user = dispatch_sync(new RegisterUserJob(
            $requestData->name,
            $requestData->email,
            $requestData->password,
        ));

        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();
        }

        return $this->spaRedirect(route('verification.notice'));
    }

    /**
     * Gestisce il tentativo di login.
     */
    #[Middleware('guest')]
    public function login(LoginRequest $request)
    {
        $throttleKey = Str::transliterate(Str::lower($request->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, self::LOGIN_MAX_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => RateLimiter::availableIn($throttleKey),
                ]),
            ]);
        }

        $provider = Auth::guard()->getProvider();
        $user = $provider->retrieveByCredentials(['email' => $request->email]);

        if (!$user || !$provider->validateCredentials($user, ['password' => $request->password])) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        if ($user->isBanned()) {
            throw ValidationException::withMessages([
                'email' => __('error.account_banned'),
            ]);
        }

        Auth::login($user, (bool) $request->remember);
        request()->session()->regenerate();

        return $this->hardRedirect(route('home'));
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        app(SessionPreserver::class)->invalidate($request);

        return $this->hardRedirect(route('home'));
    }

    #[Middleware('auth')]
    public function verificationNoticeView()
    {
        $user = Auth::user();
        if ($user != null && $user->hasVerifiedEmail()) {
            return $this->spaRedirect(route('home'));
        }

        return inertia('App/Register/VerificationNotice');
    }

    #[Middleware('auth')]
    #[Middleware('not_banned')]
    #[Middleware('signed')]
    public function emailVerification(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('home')->with('success', __('verification.verified_message'));
    }

    #[Middleware('auth')]
    #[Middleware('not_banned')]
    #[Middleware('throttle:6,1')]
    public function resendVerification(Request $request): RedirectResponse
    {
        $sent = dispatch_sync(new ResendVerificationEmailJob($request->user()));

        if (!$sent) {
            return $this->spaRedirect(route('home'));
        }

        return $this->flashSuccess(__('verification.resent'));
    }
}
