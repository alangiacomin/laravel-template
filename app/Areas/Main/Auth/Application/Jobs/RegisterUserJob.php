<?php

namespace App\Areas\Main\Auth\Application\Jobs;

use App\Areas\Main\Auth\Domain\Events\UserRegisteredEvent;
use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Foundation\Bus\Dispatchable;

class RegisterUserJob
{
    use Dispatchable;

    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public function handle(): User
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $user->assignRole(RoleEnum::USER);

        event(new UserRegisteredEvent($user));

        return $user;
    }
}
