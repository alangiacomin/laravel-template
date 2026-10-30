<?php

/** @noinspection PhpUnused */

namespace App\Areas\Admin\Roles\Presentation\Http\Controllers;

use App\Areas\Admin\Roles\Application\Jobs\UpdateRolePermissionsJob;
use App\Areas\Admin\Roles\Domain\Entities\Role;
use App\Areas\Admin\Roles\Presentation\Http\Requests\RoleUpdateRequest;
use App\Infrastructure\Http\Controllers\Controller;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as SpatieRole;

#[Middleware('auth')]
#[Middleware('verified')]
#[Middleware('not_banned')]
class RoleController extends Controller
{
    #[Middleware('can:'.GateEnum::ROLE_VIEW->value)]
    public function index()
    {
        return inertia('Admin/Roles/Roles', [
            'roles' => SpatieRole::with('permissions')->get()->map(fn (SpatieRole $role) => Role::fromModel($role)),
        ]);
    }

    #[Middleware('can:'.GateEnum::ROLE_VIEW->value)]
    public function show(int $id)
    {
        return inertia('Admin/Roles/Role', [
            'role' => Role::fromModel(SpatieRole::with('permissions')->findOrFail($id)),
        ]);
    }

    #[Middleware('can:'.GateEnum::ROLE_EDIT->value)]
    public function update(int $id, RoleUpdateRequest $request)
    {
        dispatch_sync(new UpdateRolePermissionsJob($id, (int) Auth::id(), $request->validated()['permissions'] ?? []));

        return $this->flashSuccess(__('admin.role_updated'));
    }
}
