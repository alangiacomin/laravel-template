<?php

namespace App\Areas\Admin\Roles\Presentation\Http\Controllers;

use App\Areas\Admin\Roles\Application\Jobs\UpdateRolePermissionsJob;
use App\Areas\Admin\Roles\Application\Queries\GetRoleQuery;
use App\Areas\Admin\Roles\Application\Queries\ListRolesQuery;
use App\Areas\Admin\Roles\Presentation\Http\Requests\RoleUpdateRequest;
use App\Infrastructure\Http\Controllers\Controller;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;

class RoleController extends Controller
{
    #[Middleware('can:'.GateEnum::ROLE_VIEW->value)]
    public function index(ListRolesQuery $query): Response|ResponseFactory
    {
        return inertia('Admin/Roles/Roles', [
            'roles' => $query->handle(),
        ]);
    }

    #[Middleware('can:'.GateEnum::ROLE_VIEW->value)]
    public function show(int $id, GetRoleQuery $query): Response|ResponseFactory
    {
        return inertia('Admin/Roles/Role', [
            'role' => $query->handle($id),
        ]);
    }

    #[Middleware('can:'.GateEnum::ROLE_EDIT->value)]
    public function update(int $id, RoleUpdateRequest $request): RedirectResponse
    {
        dispatch_sync(new UpdateRolePermissionsJob($id, (int) Auth::id(), $request->validated()['permissions'] ?? []));

        return $this->flashSuccess(__('admin.role_updated'));
    }
}
