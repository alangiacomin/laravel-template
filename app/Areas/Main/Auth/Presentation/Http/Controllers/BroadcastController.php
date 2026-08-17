<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;

class BroadcastController extends Controller
{
    public function join($user, $id): bool
    {
        return (int) $user->id === (int) $id;
    }
}
