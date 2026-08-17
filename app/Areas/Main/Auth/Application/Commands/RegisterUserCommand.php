<?php

namespace App\Areas\Main\Auth\Application\Commands;

use App\Areas\Main\Auth\Domain\Entities\UserItem;
use App\Areas\Main\Auth\Domain\Events\UserRegisteredEvent;
use App\Areas\Main\Auth\Domain\Repositories\IUserRepository;
use AlanGiacomin\LaravelCqrs\App\Application\Commands\SyncCommand;
use App\Shared\Infrastructure\Enums\RoleEnum;

class RegisterUserCommand extends SyncCommand
{
    private UserItem $newUser;

    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public function handle(IUserRepository $repo): void
    {
        $this->newUser = $repo->register(new UserItem(
            $this->name,
            $this->email,
            $this->password));

        $repo->assignRoles($this->newUser->id, [RoleEnum::USER]);

        event(new UserRegisteredEvent($this->newUser));
    }

    public function getResponse(): UserItem
    {
        return $this->newUser;
    }
}
