<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Shared\Infrastructure\Enums\PermissionEnum;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determina se l'utente può visualizzare qualsiasi modello.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::ROLE_READ);
    }

    /**
     * Determina se l'utente può visualizzare il modello.
     */
    public function view(User $user, Role $model): bool
    {
        return $user->can(PermissionEnum::ROLE_READ);
    }

    /**
     * Determina se l'utente può creare modelli.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determina se l'utente può aggiornare il modello.
     */
    public function update(User $user, Role $model): bool
    {
        return $user->can(PermissionEnum::ROLE_UPDATE)
            || $model->name !== RoleEnum::SUPER_ADMIN->value;
    }

    /**
     * Determina se l'utente può eliminare il modello.
     */
    public function delete(User $user, Role $model): bool
    {
        return $user->can(PermissionEnum::ROLE_DELETE);
    }

    /**
     * Determina se l'utente può ripristinare il modello.
     */
    public function restore(User $user, Role $model): bool
    {
        return false;
    }

    /**
     * Determina se l'utente può eliminare definitivamente il modello.
     */
    public function forceDelete(User $user, Role $model): bool
    {
        return false;
    }
}
