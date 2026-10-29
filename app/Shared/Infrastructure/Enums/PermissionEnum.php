<?php

namespace App\Shared\Infrastructure\Enums;

enum PermissionEnum: string
{
    case USER_CREATE = 'user_create';
    case USER_READ = 'user_read';
    case USER_UPDATE = 'user_update';
    case USER_DELETE = 'user_delete';

    case ROLE_CREATE = 'role_create';
    case ROLE_READ = 'role_read';
    case ROLE_UPDATE = 'role_update';
    case ROLE_DELETE = 'role_delete';
}
