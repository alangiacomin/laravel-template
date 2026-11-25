<?php

namespace App\Areas\Admin\Users\Presentation\Http\Controllers;

use App\Areas\Admin\Users\Application\Jobs\BlockUserJob;
use App\Areas\Admin\Users\Application\Jobs\UnblockUserJob;
use App\Areas\Admin\Users\Application\Jobs\UpdateUserJob;
use App\Areas\Admin\Users\Application\Jobs\UpdateUserRolesJob;
use App\Areas\Admin\Users\Application\Queries\GetUserQuery;
use App\Areas\Admin\Users\Application\Queries\ListUsersQuery;
use App\Areas\Admin\Users\Presentation\Http\Requests\UserUpdateRequest;
use App\Infrastructure\Http\Controllers\Controller;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;

class UserController extends Controller
{
    #[Middleware('can:'.GateEnum::USER_VIEW->value)]
    public function index(ListUsersQuery $query): Response|ResponseFactory
    {
        return inertia('Admin/Users/Users', [
            'users' => $query->handle()->withQueryString(),
        ]);
    }

    #[Middleware('can:'.GateEnum::USER_VIEW->value)]
    public function show(int $id, GetUserQuery $query): Response|ResponseFactory
    {
        return inertia('Admin/Users/User', [
            'user' => $query->handle($id),
        ]);
    }

    #[Middleware('can:'.GateEnum::USER_EDIT->value)]
    public function update(int $id, UserUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if (array_key_exists('roles', $validatedData)) {
            dispatch_sync(new UpdateUserRolesJob($id, (int) Auth::id(), $validatedData['roles']));
        }

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
