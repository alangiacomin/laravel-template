<?php

namespace App\Infrastructure\Session;

use Illuminate\Http\Request;

class SessionPreserver
{
    /**
     * @var list<string>
     */
    private const PRESERVED_KEYS = [
        'locale',
    ];

    /**
     * Invalidates the current session while preserving application session values.
     */
    public function invalidate(Request $request): void
    {
        $values = [];

        foreach (self::PRESERVED_KEYS as $key) {
            if ($request->session()->has($key)) {
                $values[$key] = $request->session()->get($key);
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        foreach ($values as $key => $value) {
            $request->session()->put($key, $value);
        }
    }
}
