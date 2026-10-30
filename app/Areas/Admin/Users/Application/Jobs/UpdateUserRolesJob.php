<?php

namespace App\Areas\Admin\Users\Application\Jobs;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Validation\ValidationException;

class UpdateUserRolesJob
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $roles
     */
    public function __construct(
        public int $id,
        public int $actorId,
        public array $roles,
    ) {}

    /**
     * Chi non è super-admin non può cambiare i ruoli privilegiati
     * (super-admin, admin) né i propri ruoli.
     */
    public function handle(): void
    {
        $target = User::findOrFail($this->id);
        $actor = User::findOrFail($this->actorId);

        $requested = array_keys(array_filter(
            $this->roles,
            fn ($granted) => filter_var($granted, FILTER_VALIDATE_BOOLEAN),
        ));

        if (!$actor->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            $current = $target->getRoleNames()->all();
            $changed = array_merge(
                array_diff($current, $requested),
                array_diff($requested, $current),
            );
            $privilegedRoles = [RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value];

            if (
                array_intersect($changed, $privilegedRoles) !== []
                || ($changed !== [] && $target->is($actor))
            ) {
                throw ValidationException::withMessages([
                    'roles' => __('validation.in', ['attribute' => 'roles']),
                ]);
            }
        }

        $target->syncRoles($requested);
    }
}
