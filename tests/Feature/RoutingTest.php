<?php

namespace Tests\Feature;

use App\Areas\Main\Auth\Infrastructure\Broadcasting\UserChannel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_segment_unknown_paths_are_not_found(): void
    {
        $this->get('/a/b/c')->assertNotFound();
        $this->get('/admin/a/b')->assertNotFound();
    }

    public function test_guest_is_redirected_to_login_on_admin_routes(): void
    {
        $this->get('/admin/utenti')->assertRedirect(route('login'));
    }

    public function test_user_channel_authorizes_only_own_id(): void
    {
        $user = User::factory()->create();
        $channel = new UserChannel();

        $this->assertTrue($channel->join($user, (string) $user->id));
        $this->assertFalse($channel->join($user, $user->id + 1));
    }

    public function test_translations_are_shared_from_the_cache_on_repeated_requests(): void
    {
        foreach ([1, 2] as $_) {
            $this->get(route('login'))
                ->assertInertia(fn ($page) => $page->has('translations.admin'));
        }
    }
}
