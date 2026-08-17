<?php

namespace App\Shared\Infrastructure\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum GateEnum: string
{
    case USER_VIEW = 'user_view';
    case USER_EDIT = 'user_edit';
    case USER_MANAGE = 'user_manage';

    case ROLE_VIEW = 'role_view';
    case ROLE_EDIT = 'role_edit';
    case ROLE_MANAGE = 'role_manage';

    case TODOS_VIEW = 'todos_view';
    case TODOS_EDIT = 'todos_edit';
    case TODOS_COMPLETE = 'todos_complete';
    case TODOS_ASSIGN = 'todos_assign';
    case TODOS_MANAGE = 'todos_manage';

    case ADMIN_ACCESS = 'admin_access';
}
