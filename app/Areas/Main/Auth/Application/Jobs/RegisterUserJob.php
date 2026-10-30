<?php

namespace App\Areas\Main\Auth\Application\Jobs;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

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
        $user = DB::transaction(function (): User {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            $user->assignRole(RoleEnum::USER);

            return $user;
        });

        event(new Registered($user));

        return $user;
    }
}
