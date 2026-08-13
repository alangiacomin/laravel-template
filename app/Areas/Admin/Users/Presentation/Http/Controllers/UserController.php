<?php

namespace App\Areas\Admin\Users\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Admin\Users\Application\Commands\BloccaUtenteCommand;
use App\Areas\Admin\Users\Application\Commands\SbloccaUtenteCommand;
use App\Areas\Admin\Users\Application\Data\AdminUserData;
use App\Areas\Admin\Users\Presentation\Http\Requests\UserUpdateRequest;
use App\Areas\Main\Auth\Domain\Repositories\IUserRepository;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
#[Middleware('not_banned')]
class UserController extends Controller
{
    #[GateAuthorize(GateEnum::USER_VIEW)]
    public function index(IUserRepository $userRepository)
    {
        // $usersWithRoles = $userRepository->allWithRoles();
        $usersWithRoles = $userRepository->all();

        return inertia('Admin/Users/Users', [
            'users' => $usersWithRoles->map(fn ($item) => AdminUserData::fromUserItem($item['user'], $item['roles'])
            ),
        ]);
    }

    #[GateAuthorize(GateEnum::USER_VIEW)]
    public function show(int $id, IUserRepository $userRepository)
    {
        return inertia('Admin/Users/User', [
            'user' => AdminUserData::fromUserItem($userRepository->get($id), $userRepository->getRoles($id)->toArray()),
        ]);
    }

    #[GateAuthorize(GateEnum::USER_EDIT)]
    public function update(int $id, UserUpdateRequest $request, IUserRepository $userRepository)
    {
        $validatedData = $request->validated();
        $userRepository->update($id, $validatedData);

        $userRepository->assignRoles($id, array_keys(array_filter($validatedData['roles'] ?? [])));
    }

    #[GateAuthorize(GateEnum::USER_MANAGE)]
    public function blocca(int $id)
    {
        $this->execute(new BloccaUtenteCommand($id));

        return $this->flashSuccess('Utente bloccato con successo');
    }

    #[GateAuthorize(GateEnum::USER_MANAGE)]
    public function sblocca(int $id)
    {
        $this->execute(new SbloccaUtenteCommand($id));

        return $this->flashSuccess('Utente sbloccato con successo');
    }
}
