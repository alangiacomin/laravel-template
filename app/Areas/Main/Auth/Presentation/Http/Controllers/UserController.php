<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Main\Auth\Presentation\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Routing\Attributes\Controllers\Middleware;

class UserController extends Controller
{
    #[Middleware('auth')]
    #[Middleware('not_banned')]
    public function update(int $id, UserUpdateRequest $request)
    {
        $validatedData = $request->validated();
        User::findOrFail($id)->update($validatedData);
    }
}
