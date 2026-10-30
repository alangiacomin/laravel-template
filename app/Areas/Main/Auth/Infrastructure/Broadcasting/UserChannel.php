<?php

namespace App\Areas\Main\Auth\Infrastructure\Broadcasting;

use App\Models\User;

class UserChannel
{
    public function join(User $user, int|string $id): bool
    {
        return (int) $user->id === (int) $id;
    }
}
