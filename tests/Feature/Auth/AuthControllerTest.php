<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_login_view_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_register_view_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
    }

    public function test_a_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_user_cannot_login_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_banned_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'banned_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_requires_valid_data(): void
    {
        $response = $this->post('/login', [
            'email' => 'not-an-email',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_a_user_can_register(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'mario.rossi@example.com',
        ]);

        $user = User::where('email', 'mario.rossi@example.com')->first();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        Notification::fake();

        $existing = User::factory()->create();

        $response = $this->post('/register', [
            'name' => 'Mario Rossi',
            'email' => $existing->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_a_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/logout');

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_guest_cannot_view_user_page(): void
    {
        $response = $this->get('/user');

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_user_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/user');

        $response->assertOk();
    }

    public function test_banned_user_cannot_view_user_page(): void
    {
        $user = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/user');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_from_verification_notice(): void
    {
        $response = $this->get('/email/verify');

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_verified_user_is_redirected_from_verification_notice(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertRedirect();
    }

    public function test_unverified_user_can_view_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertOk();
    }

    public function test_guest_cannot_update_a_user_profile(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->patch("/user/{$user->id}/update", [
            'name' => 'New Name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Old Name',
        ]);
    }

    public function test_authenticated_user_can_update_their_own_profile(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->patch("/user/{$user->id}/update", [
            'name' => 'New Name',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_update_profile_requires_a_valid_name(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->patch("/user/{$user->id}/update", [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Old Name',
        ]);
    }

    public function test_banned_user_cannot_update_a_user_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($user)->patch("/user/{$user->id}/update", [
            'name' => 'New Name',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Old Name',
        ]);
    }
}
