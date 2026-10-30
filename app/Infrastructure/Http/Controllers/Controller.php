<?php

namespace App\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    public function flashSuccess(mixed $returnValue): RedirectResponse
    {
        return back()->with('success', $returnValue);
    }

    public function spaRedirect(string $route): RedirectResponse
    {
        return redirect($route);
    }

    public function hardRedirect(string $route): Response
    {
        return Inertia::location(redirect($route)->getTargetUrl());
    }

    /**
     * Da usare solo dopo il login: torna alla destinazione richiesta in origine, se presente.
     */
    public function hardIntendedRedirect(string $default): Response
    {
        return Inertia::location(redirect()->intended($default)->getTargetUrl());
    }
}
