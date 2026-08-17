<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Shared\Infrastructure\Enums\PermissionEnum;

class UserPolicy
{
    /**
     * Determina se l'utente può visualizzare qualsiasi modello.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::USER_READ);
    }

    /**
     * Determina se l'utente può visualizzare il modello.
     */
    public function view(User $user, User $model): bool
    {
        return $user->id == $model->id;
    }

    /**
     * Determina se l'utente può creare modelli.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::USER_CREATE);
    }

    /**
     * Determina se l'utente può aggiornare il modello.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::USER_UPDATE)
            || $user->id == $model->id;
    }

    /**
     * Determina se l'utente può eliminare il modello.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::USER_DELETE);
    }

    /**
     * Determina se l'utente può ripristinare il modello.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determina se l'utente può eliminare definitivamente il modello.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
