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
        $this->post(route('language.update'), ['locale' => 'en'])
            ->assertRedirect();

        $this->assertSame('en', session('locale'));
        $this->withSession(['locale' => 'en'])
            ->get('/')
            ->assertOk();
    }

    public function test_french_falls_back_to_italian(): void
    {
        $this->post(route('language.update'), ['locale' => 'fr'])
            ->assertRedirect();

        $this->assertSame('it', session('locale'));
    }

    public function test_english_locale_survives_logout(): void
    {
        $user = User::factory()->create();

        $this->withSession(['locale' => 'en'])
            ->actingAs($user)
            ->get(route('logout'))
            ->assertRedirect();

        $this->assertGuest();
        $this->assertSame('en', session('locale'));
    }
}
