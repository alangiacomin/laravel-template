<?php

namespace App\Areas\Main;

use App\Infrastructure\Http\Controllers\Controller;

class FallbackController extends Controller
{
    public function notFound(): never
    {
        abort(404);
    }
}
