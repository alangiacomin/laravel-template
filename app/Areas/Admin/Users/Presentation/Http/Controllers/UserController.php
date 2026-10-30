<?php

namespace App\Areas\Admin\Users\Presentation\Http\Controllers;

use App\Areas\Admin\Users\Application\Data\AdminUserData;
use App\Areas\Admin\Users\Application\Jobs\BloccaUtenteJob;
use App\Areas\Admin\Users\Application\Jobs\SbloccaUtenteJob;
use App\Areas\Admin\Users\Presentation\Http\Requests\UserUpdateRequest;
use App\Infrastructure\Http\Controllers\Controller;
use App\Models\User;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

#[Middleware('auth')]
#[Middleware('verified')]
#[Middleware('not_banned')]
class UserController extends Controller
{
    #[Middleware('can:'.GateEnum::USER_VIEW->value)]
    public function index()
    {
        $users = User::with('roles')->get();

        return inertia('Admin/Users/Users', [
            'users' => $users->map(fn (User $user) => AdminUserData::fromModel($user)),
        ]);
    }

    #[Middleware('can:'.GateEnum::USER_VIEW->value)]
    public function show(int $id)
    {
        $user = User::with('roles')->findOrFail($id);

        return inertia('Admin/Users/User', [
            'user' => AdminUserData::fromModel($user),
        ]);
    }

    #[Middleware('can:'.GateEnum::USER_EDIT->value)]
    public function update(int $id, UserUpdateRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::findOrFail($id);
        $user->update($validatedData);

        $user->syncRoles(array_keys(array_filter($validatedData['roles'] ?? [])));
    }

    #[Middleware('can:'.GateEnum::USER_MANAGE->value)]
    public function blocca(int $id)
    {
        dispatch_sync(new BloccaUtenteJob($id, (int) Auth::id()));

        return $this->flashSuccess(__('admin.user_blocked'));
    }

    #[Middleware('can:'.GateEnum::USER_MANAGE->value)]
    public function sblocca(int $id)
    {
        dispatch_sync(new SbloccaUtenteJob($id));

        return $this->flashSuccess(__('admin.user_unblocked'));
    }
}
