<?php

namespace App\Areas\Main\Auth\Infrastructure\Inertia;

use App\Areas\Main\Auth\Application\Data\UserData;
use App\Areas\Main\Auth\Application\Inertia\AbilityResolver;
use App\Models\User;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Support\Facades\Gate;

class GateAbilityResolver implements AbilityResolver
{
    public function forUser(?UserData $user): array
    {
        if (!$user) {
            return [];
        }

        $model = User::find($user->id);

        if (!$model) {
            return [];
        }

        $gate = Gate::forUser($model);

        return collect(GateEnum::cases())
            ->filter(fn ($ability) => $gate->allows($ability))
            ->map(fn ($ability) => $ability->value)
            ->values()
            ->all();
    }
}
