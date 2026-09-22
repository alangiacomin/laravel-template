<?php

/** @noinspection PhpUnused */

namespace App\Areas\Admin\Roles\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Admin\Roles\Domain\Entities\Role;
use App\Areas\Admin\Roles\Presentation\Http\Requests\RoleUpdateRequest;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role as SpatieRole;

#[Middleware('auth')]
#[Middleware('not_banned')]
class RoleController extends Controller
{
    public function index()
    {
        return inertia('Admin/Roles/Roles', [
            'roles' => SpatieRole::with('permissions')->get()->map(fn (SpatieRole $role) => Role::fromModel($role)),
        ]);
    }

    public function show(int $id)
    {
        return inertia('Admin/Roles/Role', [
            'role' => Role::fromModel(SpatieRole::with('permissions')->findOrFail($id)),
        ]);
    }

    #[GateAuthorize(GateEnum::ROLE_EDIT)]
    public function update(int $id, RoleUpdateRequest $request)
    {
        $validatedData = $request->validated();
        $permissions = Arr::divide(Arr::where($validatedData['permissions'], fn ($enabled) => $enabled))[0];

        SpatieRole::findOrFail($id)->syncPermissions($permissions);

        return $this->flashSuccess(__('admin.role_updated'));
    }
}
