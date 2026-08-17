<?php

namespace App\Infrastructure\Providers;

use App\Areas\Main\Auth\Application\Inertia\AbilityResolver;
use App\Areas\Main\Auth\Infrastructure\Inertia\GateAbilityResolver;
use App\Models\User;
use App\Shared\Infrastructure\Enums\GateEnum;
use App\Shared\Infrastructure\Enums\PermissionEnum;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AbilityResolver::class, GateAbilityResolver::class);

        // $this->app->bind(CommandBus::class, function () {
        //     return new CommandBus();
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // JsonResource::withoutWrapping();

        Queue::before(function ($event) {
            if ($locale = Context::get('locale')) {
                app()->setLocale($locale);
            }
        });
        //
        //
        // Queue::before(
        //     function (/* JobProcessing $event */) {
        //         DB::beginTransaction();
        //         // $event->connectionName
        //         // $event->job
        //         // $event->job->payload()
        //     }
        // );
        //
        // Queue::after(
        //     function (/* JobProcessed $event */) {
        //         DB::commit();
        //         // $event->connectionName
        //         // $event->job
        //         // $event->job->payload()
        //     }
        // );

        Gate::before(
            function (User $user) {
                if ($user->isBanned()) {
                    return false;
                }

                return $user->hasRole(RoleEnum::SUPER_ADMIN)
                    ? true
                    : null;
            }
        );

        Gate::define(GateEnum::USER_VIEW, fn (User $user) => $user->can([
            PermissionEnum::USER_READ,
        ]));
        Gate::define(GateEnum::USER_EDIT, fn (User $user) => $user->can([
            PermissionEnum::USER_READ,
            PermissionEnum::USER_UPDATE,
        ]));
        Gate::define(GateEnum::USER_MANAGE, fn (User $user) => $user->can([
            PermissionEnum::USER_CREATE,
            PermissionEnum::USER_READ,
            PermissionEnum::USER_UPDATE,
            PermissionEnum::USER_DELETE,
        ]));

        Gate::define(GateEnum::ROLE_VIEW, fn (User $user) => $user->can([
            PermissionEnum::ROLE_READ,
        ]));
        Gate::define(GateEnum::ROLE_EDIT, fn (User $user) => $user->can([
            PermissionEnum::ROLE_READ,
            PermissionEnum::ROLE_UPDATE,
        ]));
        Gate::define(GateEnum::ROLE_MANAGE, fn (User $user) => $user->can([
            PermissionEnum::ROLE_CREATE,
            PermissionEnum::ROLE_READ,
            PermissionEnum::ROLE_UPDATE,
            PermissionEnum::ROLE_DELETE,
        ]));

        Gate::define(GateEnum::TODOS_VIEW, fn (User $user) => $user->can([
            PermissionEnum::TODOS_READ,
        ]));
        Gate::define(GateEnum::TODOS_EDIT, fn (User $user) => $user->can([
            PermissionEnum::TODOS_READ,
            PermissionEnum::TODOS_UPDATE,
        ]));
        Gate::define(GateEnum::TODOS_MANAGE, fn (User $user) => $user->can([
            PermissionEnum::TODOS_CREATE,
            PermissionEnum::TODOS_READ,
            PermissionEnum::TODOS_UPDATE,
            PermissionEnum::TODOS_DELETE,
        ]));

        Gate::define(GateEnum::ADMIN_ACCESS, fn (User $user) => Gate::any([
            GateEnum::USER_VIEW,
            GateEnum::ROLE_VIEW,
            GateEnum::TODOS_VIEW,
        ]));
    }
}
