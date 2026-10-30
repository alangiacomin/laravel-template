<?php

namespace App\Areas\Main\Auth\Application\Jobs;

use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;

class ResendVerificationEmailJob
{
    use Dispatchable;

    public function __construct(public User $user) {}

    public function handle(): bool
    {
        if ($this->user->hasVerifiedEmail()) {
            return false;
        }

        $this->user->sendEmailVerificationNotification();

        return true;
    }
}
