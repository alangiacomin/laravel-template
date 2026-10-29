<?php

namespace App\Shared\Infrastructure\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super-admin';

    case ADMIN = 'admin';

    case EDITOR = 'editor';

    case USER = 'user';
}
