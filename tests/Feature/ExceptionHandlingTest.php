<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->group(function (): void {
            Route::get('/_test/forbidden', fn () => throw new HttpException(403, 'Vietato'));
            Route::get('/_test/boom', fn () => throw new RuntimeException('segreto-interno'));
            Route::post('/_test/not-found', fn () => throw new NotFoundHttpException('No query results for model [App\\Models\\User]'));
            Route::post('/_test/unauthorized', fn () => throw new HttpException(403, 'This action is unauthorized.'));
            Route::post('/_test/too-many', fn () => throw new HttpException(429, 'Too Many Attempts.'));
            Route::post('/_test/server-error', fn () => throw new HttpException(500, 'dettaglio-interno'));
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

    public function test_inertia_non_get_flash_uses_translated_generic_messages(): void
    {
        config(['app.debug' => false]);

        $cases = [
            '/_test/not-found' => __('error.404_message'),
            '/_test/unauthorized' => __('error.403_message'),
            '/_test/too-many' => __('error.429_message'),
            '/_test/server-error' => __('error.500_message'),
        ];

        foreach ($cases as $uri => $expected) {
            $this->withHeaders(['X-Inertia' => 'true'])
                ->from('/_test/forbidden')
                ->post($uri)
                ->assertSessionHas('error', $expected);
        }
    }
}
