<?php

namespace App\Areas\Main\Auth\Presentation\Http\Controllers;

use App\Infrastructure\Http\Controllers\Controller;

class BroadcastController extends Controller
{
    public function join($user, $id): bool
    {
        return (int) $user->id === (int) $id;
    }
}
