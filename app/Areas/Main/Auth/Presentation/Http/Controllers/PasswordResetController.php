<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use App\Areas\Main\Auth\Application\Jobs\ResetPasswordJob;
use App\Areas\Main\Auth\Application\Jobs\SendPasswordResetLinkJob;
use App\Areas\Main\Auth\Presentation\Http\Requests\ForgotPasswordRequest;
use App\Areas\Main\Auth\Presentation\Http\Requests\ResetPasswordRequest;
use App\Infrastructure\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Inertia\ResponseFactory;

class PasswordResetController extends Controller
{
    #[Middleware('guest')]
    public function forgotPasswordView(): Response|ResponseFactory
    {
        return inertia('App/ForgotPassword/ForgotPassword');
    }

    /**
     * La risposta è sempre generica per non rivelare se l'email esiste.
     */
    #[Middleware('guest')]
    #[Middleware('throttle:6,1')]
    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        dispatch_sync(new SendPasswordResetLinkJob($request->email));

        return $this->flashSuccess(__('passwords.sent'));
    }

    #[Middleware('guest')]
    public function resetPasswordView(Request $request, string $token): Response|ResponseFactory
    {
        return inertia('App/ResetPassword/ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * @throws ValidationException
     */
    #[Middleware('guest')]
    #[Middleware('throttle:6,1')]
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = dispatch_sync(new ResetPasswordJob(
            $request->token,
            $request->email,
            $request->password,
        ));

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        return redirect()->route('login')->with('success', __($status));
    }
}
