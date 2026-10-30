<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use App\Areas\Main\Auth\Presentation\Http\Requests\UserUpdateRequest;
use App\Infrastructure\Http\Controllers\Controller;
use Illuminate\Routing\Attributes\Controllers\Middleware;

class UserController extends Controller
{
    #[Middleware('auth')]
    #[Middleware('verified')]
    #[Middleware('not_banned')]
    public function update(UserUpdateRequest $request)
    {
        $validatedData = $request->validated();
        $request->user()->update($validatedData);
    }
}
