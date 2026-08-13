<?php

/** @noinspection PhpUnused */

namespace App\Areas\Admin\Roles\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Admin\Roles\Domain\Repositories\IRoleRepository;
use App\Areas\Admin\Roles\Presentation\Http\Requests\RoleUpdateRequest;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Arr;

#[Middleware('auth')]
#[Middleware('not_banned')]
class RoleController extends Controller
{
    #[GateAuthorize(GateEnum::ROLE_VIEW)]
    public function index(IRoleRepository $roleRepository)
    {
        return inertia('Admin/Roles/Roles', [
            'roles' => $roleRepository->all(),
        ]);
    }

    #[GateAuthorize(GateEnum::ROLE_EDIT)]
    public function show(int $id, IRoleRepository $roleRepository)
    {
        return inertia('Admin/Roles/Role', [
            'role' => $roleRepository->get($id),
        ]);
    }

    #[GateAuthorize(GateEnum::ROLE_EDIT)]
    public function update(int $id, RoleUpdateRequest $request, IRoleRepository $roleRepository)
    {
        $validatedData = $request->validated();
        $permissions = Arr::divide(Arr::where($validatedData['permissions'], fn ($enabled) => $enabled))[0];

        $roleRepository->updatePermissions($id, $permissions);

        return $this->flashSuccess('Ruolo aggiornato con successo.');
    }
}
