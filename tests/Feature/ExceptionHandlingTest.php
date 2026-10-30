<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->group(function (): void {
            Route::get('/_test/forbidden', fn () => throw new HttpException(403, 'Vietato'));
            Route::get('/_test/boom', fn () => throw new RuntimeException('segreto-interno'));
            Route::post('/_test/forbidden', fn () => throw new HttpException(403, 'Azione non consentita'));
        });
    }

    public function test_http_exception_renders_inertia_error_page_with_status(): void
    {
        $this->get('/_test/forbidden')
            ->assertForbidden()
            ->assertInertia(fn ($page) => $page
                ->component('App/ErrorPage/ErrorPage')
                ->where('status', 403));
    }

    public function test_generic_exception_does_not_leak_message(): void
    {
        config(['app.debug' => false]);

        $this->get('/_test/boom')
            ->assertStatus(500)
            ->assertDontSee('segreto-interno');
    }

    public function test_inertia_non_get_http_exception_flashes_error(): void
    {
        $this->withHeaders(['X-Inertia' => 'true'])
            ->from('/_test/forbidden')
            ->post('/_test/forbidden')
            ->assertRedirect('/_test/forbidden')
            ->assertSessionHas('error', 'Azione non consentita');
    }
}
