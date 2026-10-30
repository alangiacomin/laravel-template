<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use App\Areas\Main\Auth\Application\Jobs\UpdateProfileJob;
use App\Areas\Main\Auth\Presentation\Http\Requests\UserUpdateRequest;
use App\Infrastructure\Http\Controllers\Controller;
use Illuminate\Routing\Attributes\Controllers\Middleware;

class UserController extends Controller
{
    #[Middleware('auth')]
    #[Middleware('verified')]
    public function update(UserUpdateRequest $request)
    {
        dispatch_sync(new UpdateProfileJob($request->user(), $request->validated()['name']));

        return $this->flashSuccess(__('user.profile_updated'));
    }
}
