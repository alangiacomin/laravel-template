<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Main\Auth\Application\Commands\RegisterUserCommand;
use App\Areas\Main\Auth\Application\Data\UserData;
use App\Areas\Main\Auth\Domain\Repositories\IUserRepository;
use App\Areas\Main\Auth\Infrastructure\Mappers\UserItemMapper;
use App\Areas\Main\Auth\Presentation\Http\Requests\LoginRequest;
use App\Areas\Main\Auth\Presentation\Http\Requests\RegisterRequest;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Inertia\ResponseFactory;

class AuthController extends Controller
{
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
    #[Middleware('not_banned')]
    #[GateAuthorize(GateEnum::USER_VIEW)]
    public function userView(): Response|ResponseFactory
    {
        return inertia('App/User/User', [
            'user' => UserData::From(UserItemMapper::toDomain(Auth::user())),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    #[Middleware('guest')]
    public function register(RegisterRequest $requestData, IUserRepository $userRepository): RedirectResponse
    {
        $user = $this->execute(new RegisterUserCommand(
            $requestData->name,
            $requestData->email,
            $requestData->password,
        ));

        if ($user) {
            Auth::login(UserItemMapper::toPersistence($userRepository->get($user->id)));
        }

        return $this->spaRedirect($this->routeGenerator()->route('verification.notice'));
    }

    /**
     * Gestisce il tentativo di login.
     */
    #[Middleware('guest')]
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        if ($user->isBanned()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => __('error.account_banned'),
            ]);
        }

        request()->session()->regenerate();

        return $this->hardRedirect($this->routeGenerator()->route('home'));
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->hardRedirect($this->routeGenerator()->route('home'));
    }

    #[Middleware('auth')]
    public function verificationNoticeView()
    {
        $user = Auth::user();
        if ($user != null && $user->hasVerifiedEmail()) {
            return $this->spaRedirect($this->routeGenerator()->route('home'));
        }

        return inertia('App/Register/VerificationNotice');
    }

    #[Middleware('auth')]
    #[Middleware('not_banned')]
    #[Middleware('signed')]
    public function emailVerification(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return inertia('App/Register/VerificationVerify');
    }
}
