<?php

namespace App\Areas\Main\Auth\Application\Inertia;

use App\Areas\Main\Auth\Application\Data\UserData;

interface AbilityResolver
{
    /**
     * @return list<string>
     */
    public function forUser(?UserData $user): array;
}
