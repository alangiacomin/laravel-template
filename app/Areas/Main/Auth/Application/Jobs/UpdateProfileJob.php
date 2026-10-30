<?php

namespace App\Areas\Main\Auth\Application\Jobs;

use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProfileJob
{
    use Dispatchable;

    public function __construct(
        public User $user,
        public string $name,
    ) {}

    public function handle(): void
    {
        $this->user->update(['name' => $this->name]);
    }
}
