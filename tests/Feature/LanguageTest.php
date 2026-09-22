<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_change_and_persist_the_english_locale(): void
    {
        $this->post('/it/language', ['locale' => 'en'])
            ->assertRedirect();

        $this->assertSame('en', session('locale'));
        $this->withSession(['locale' => 'en'])
            ->get('/en')
            ->assertOk();
    }

    public function test_french_falls_back_to_italian(): void
    {
        $this->post('/it/language', ['locale' => 'fr'])
            ->assertRedirect();

        $this->assertSame('it', session('locale'));
    }

    public function test_english_locale_survives_logout(): void
    {
        $user = User::factory()->create();

        $this->withSession(['locale' => 'en'])
            ->actingAs($user)
            ->get('/en/logout')
            ->assertRedirect();

        $this->assertGuest();
        $this->assertSame('en', session('locale'));
    }

    public function test_root_redirects_to_the_session_locale(): void
    {
        $this->withSession(['locale' => 'en'])
            ->get('/')
            ->assertRedirect('/en');
    }

    public function test_localized_routes_set_the_application_locale(): void
    {
        $this->get('/en/example-page')
            ->assertOk();

        $this->assertSame('en', app()->getLocale());
        $this->assertSame('en', session('locale'));
    }

    public function test_route_slugs_are_translated_per_locale(): void
    {
        $this->get('/it/registrati')->assertOk();
        $this->get('/en/register')->assertOk();
        $this->get('/it/login')->assertNotFound();
    }

    public function test_legacy_routes_remain_available_using_the_session_locale(): void
    {
        $this->withSession(['locale' => 'en'])
            ->get('/example-page')
            ->assertRedirect('/en/example-page');
    }

    public function test_language_switch_redirects_to_the_equivalent_current_page(): void
    {
        $this->withSession(['locale' => 'it'])
            ->from('/it/example-page?tab=details')
            ->post('/it/language', ['locale' => 'en'])
            ->assertRedirect('/en/example-page?tab=details');
    }

    public function test_legacy_urls_redirect_to_the_localized_slug(): void
    {
        $this->get('/login')->assertRedirect('/it/accedi');
    }

    public function test_already_localized_unknown_paths_do_not_receive_another_locale_prefix(): void
    {
        $this->get('/it/.well-known/appspecific/com.chrome.devtools.json')
            ->assertNotFound();
    }

    public function test_well_known_requests_are_not_redirected_to_the_application_locale(): void
    {
        $this->get('/.well-known/appspecific/com.chrome.devtools.json')
            ->assertNotFound();
    }
}
