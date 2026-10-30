<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_view_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::EDITOR->value);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_dashboard_stats_are_aggregated(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 15));

        $admin = User::factory()->create(['created_at' => now()->subMonthNoOverflow()]);
        $admin->assignRole(RoleEnum::SUPER_ADMIN->value);
        User::factory()->create(['created_at' => now()]);
        User::factory()->unverified()->create(['created_at' => now()]);
        User::factory()->unverified()->create(['created_at' => now()->subMonthNoOverflow()]);
        User::factory()->create(['created_at' => now()->subMonths(3)]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Dashboard/Dashboard')
                ->where('dashboard.users_total_count', 5)
                ->where('dashboard.users_total_count_unverified', 2)
                ->where('dashboard.users_month_count', 2)
                ->where('dashboard.users_month_count_unverified', 1)
                ->where('dashboard.users_last_month_count', 2)
                ->where('dashboard.users_last_month_count_unverified', 1));
    }

    public function test_user_without_admin_access_cannot_view_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::USER->value);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_banned_user_cannot_view_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }
}
