<?php

namespace App\Areas\Main\Auth\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Main\Auth\Domain\Events\UserRegisteredEvent;
use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;

class RegisterUserCommand extends Command
{
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
