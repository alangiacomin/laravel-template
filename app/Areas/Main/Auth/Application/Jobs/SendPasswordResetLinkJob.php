<?php

namespace App\Areas\Main\Auth\Application\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkJob
{
    use Dispatchable;

    public function __construct(public string $email) {}

    public function handle(): string
    {
        return Password::broker()->sendResetLink(['email' => $this->email]);
    }
}
