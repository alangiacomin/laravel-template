<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Main\Auth\Domain\Repositories\IUserRepository;
use App\Areas\Main\Auth\Presentation\Http\Requests\UserUpdateRequest;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Routing\Attributes\Controllers\Middleware;

class UserController extends Controller
{
    #[Middleware('auth')]
    #[Middleware('not_banned')]
    #[GateAuthorize(GateEnum::USER_EDIT)]
    public function update(int $id, UserUpdateRequest $request, IUserRepository $userRepository)
    {
        $validatedData = $request->validated();
        $userRepository->update($id, $validatedData);
    }
}
