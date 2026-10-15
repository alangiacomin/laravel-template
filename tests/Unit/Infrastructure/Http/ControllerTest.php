<?php

namespace Tests\Unit\Infrastructure\Http;

use App\Infrastructure\Http\Controllers\Controller;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    public function test_flash_success_redirects_back_with_success_message(): void
    {
        $response = $this->controller()->flashSuccess('done');

        $this->assertSame('done', $response->getSession()->get('success'));
    }

    public function test_spa_redirect_redirects_to_given_route(): void
    {
        $response = $this->controller()->spaRedirect('/dashboard');

        $this->assertSame(url('/dashboard'), $response->getTargetUrl());
    }

    public function test_hard_redirect_redirects_to_given_url(): void
    {
        $response = $this->controller()->hardRedirect('/login');

        $this->assertSame(url('/login'), $response->headers->get('Location'));
    }

    private function controller(): Controller
    {
        return new class() extends Controller {};
    }
}
