<?php

namespace App\Areas\Main\Auth\Domain\Events;

use App\Areas\Main\Auth\Domain\Entities\UserItem;

readonly class UserRegisteredEvent
{
    public function __construct(
        public UserItem $user,
    ) {}
}
