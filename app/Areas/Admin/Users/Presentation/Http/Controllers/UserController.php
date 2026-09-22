<?php

namespace App\Areas\Admin\Users\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Admin\Users\Application\Commands\BloccaUtenteCommand;
use App\Areas\Admin\Users\Application\Commands\SbloccaUtenteCommand;
use App\Areas\Admin\Users\Application\Data\AdminUserData;
use App\Areas\Admin\Users\Presentation\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
#[Middleware('not_banned')]
class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();

        return inertia('Admin/Users/Users', [
            'users' => $users->map(fn (User $user) => AdminUserData::fromModel($user)),
        ]);
    }

    public function show(int $id)
    {
        $user = User::with('roles')->findOrFail($id);

        return inertia('Admin/Users/User', [
            'user' => AdminUserData::fromModel($user),
        ]);
    }

    #[GateAuthorize(GateEnum::USER_EDIT)]
    public function update(int $id, UserUpdateRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::findOrFail($id);
        $user->update($validatedData);

        $user->syncRoles(array_keys(array_filter($validatedData['roles'] ?? [])));
    }

    #[GateAuthorize(GateEnum::USER_MANAGE)]
    public function blocca(int $id)
    {
        dispatch_sync(new BloccaUtenteCommand($id));

        return $this->flashSuccess(__('admin.user_blocked'));
    }

    #[GateAuthorize(GateEnum::USER_MANAGE)]
    public function sblocca(int $id)
    {
        dispatch_sync(new SbloccaUtenteCommand($id));

        return $this->flashSuccess(__('admin.user_unblocked'));
    }
}
