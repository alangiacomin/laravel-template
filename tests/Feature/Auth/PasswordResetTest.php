<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_forgot_password_view_can_be_rendered(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_reset_password_view_can_be_rendered(): void
    {
        $this->get(route('password.reset', ['token' => 'abc', 'email' => 'a@example.com']))->assertOk();
    }

    public function test_authenticated_user_cannot_view_forgot_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('password.request'))->assertRedirect();
    }

    public function test_reset_link_is_sent_for_existing_email(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('success', __('passwords.sent'));
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_unknown_email_gets_the_same_response_and_no_mail(): void
    {
        Notification::fake();

        $response = $this->post(route('password.email'), ['email' => 'unknown@example.com']);

        $response->assertRedirect();
        $response->assertSessionHas('success', __('passwords.sent'));
        $response->assertSessionHasNoErrors();
        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-token']);
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertTrue(Hash::check('new-password-123', $user->password));
        $this->assertNotSame('old-token', $user->remember_token);
    }

    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors(['email' => __('passwords.token')]);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_token_cannot_be_reused(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        $payload = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ];

        $this->post(route('password.update'), $payload)->assertRedirect(route('login'));
        $this->post(route('password.update'), $payload)->assertSessionHasErrors('email');
    }

    public function test_password_must_respect_the_rules(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_banned_user_can_reset_password_but_cannot_login(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['banned_at' => now()])->save();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'new-password-123',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_reset_link_request_is_throttled(): void
    {
        Notification::fake();

        for ($i = 0; $i < 6; $i++) {
            $this->post(route('password.email'), ['email' => 'a@example.com'])->assertRedirect();
        }

        $this->post(route('password.email'), ['email' => 'a@example.com'])->assertStatus(429);
    }
}
