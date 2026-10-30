<?php

namespace App\Areas\Admin\Users\Presentation\Http\Controllers;

use App\Areas\Admin\Users\Application\Data\AdminUserData;
use App\Areas\Admin\Users\Application\Jobs\BlockUserJob;
use App\Areas\Admin\Users\Application\Jobs\UnblockUserJob;
use App\Areas\Admin\Users\Application\Jobs\UpdateUserJob;
use App\Areas\Admin\Users\Application\Jobs\UpdateUserRolesJob;
use App\Areas\Admin\Users\Presentation\Http\Requests\UserUpdateRequest;
use App\Infrastructure\Http\Controllers\Controller;
use App\Models\User;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;

#[Middleware('auth')]
#[Middleware('verified')]
class UserController extends Controller
{
    #[Middleware('can:'.GateEnum::USER_VIEW->value)]
    public function index(): Response|ResponseFactory
    {
        $users = User::with('roles')->orderBy('id')->paginate(15)->withQueryString();

        return inertia('Admin/Users/Users', [
            'users' => $users->through(fn (User $user) => AdminUserData::fromModel($user)),
        ]);
    }

    #[Middleware('can:'.GateEnum::USER_VIEW->value)]
    public function show(int $id): Response|ResponseFactory
    {
        $user = User::with('roles')->findOrFail($id);

        return inertia('Admin/Users/User', [
            'user' => AdminUserData::fromModel($user),
        ]);
    }

    #[Middleware('can:'.GateEnum::USER_EDIT->value)]
    public function update(int $id, UserUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        dispatch_sync(new UpdateUserRolesJob($id, (int) Auth::id(), $validatedData['roles'] ?? []));

        dispatch_sync(new UpdateUserJob($id, $validatedData['name']));

        return $this->flashSuccess(__('admin.user_updated'));
    }

    #[Middleware('can:'.GateEnum::USER_MANAGE->value)]
    public function block(int $id): RedirectResponse
    {
        dispatch_sync(new BlockUserJob($id, (int) Auth::id()));

        return $this->flashSuccess(__('admin.user_blocked'));
    }

    #[Middleware('can:'.GateEnum::USER_MANAGE->value)]
    public function unblock(int $id): RedirectResponse
    {
        dispatch_sync(new UnblockUserJob($id));

        return $this->flashSuccess(__('admin.user_unblocked'));
    }
}
