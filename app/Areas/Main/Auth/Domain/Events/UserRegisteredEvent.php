<?php

namespace App\Areas\Main\Auth\Domain\Events;

use App\Models\User;

readonly class UserRegisteredEvent
{
    public function __construct(
        public User $user,
    ) {}
}
