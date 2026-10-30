<?php

namespace App\Areas\Main\Auth\Application\Jobs;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordJob
{
    use Dispatchable;

    public function __construct(
        public string $token,
        public string $email,
        public string $password,
    ) {}

    public function handle(): string
    {
        return Password::broker()->reset(
            [
                'token' => $this->token,
                'email' => $this->email,
                'password' => $this->password,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );
    }
}
