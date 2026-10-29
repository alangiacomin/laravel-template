<?php

namespace Tests\Unit;

use Tests\TestCase;

class LocaleConfigTest extends TestCase
{
    public function test_locale_and_fallback_locale_default_to_italian(): void
    {
        $this->assertSame('it', config('app.locale'));
        $this->assertSame('it', config('app.fallback_locale'));
    }
}
