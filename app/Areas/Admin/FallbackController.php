<?php

namespace App\Areas\Admin;

use App\Infrastructure\Http\Controllers\Controller;

class FallbackController extends Controller
{
    public function notFound(): never
    {
        abort(404);
    }
}
