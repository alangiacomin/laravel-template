<?php

namespace App\Areas\Main\Auth\Domain\Entities;

use AlanGiacomin\LaravelCqrs\App\Domain\Entities\DomainItem;
use Carbon\Carbon;
use InvalidArgumentException;

class UserItem extends DomainItem
{
    public private(set) int $id;

    public private(set) string $name;

    public private(set) string $email;

    public private(set) string $password;

    public private(set) bool $isVerified;

    public private(set) bool $isBanned;

    public private(set) ?string $avatar;

    public private(set) ?Carbon $createdAt;

    // public private(set) array $roles;

    public function __construct(
        string $name,
        string $email,
        string $password,
        int $id = 0,
        bool $isVerified = false,
        bool $isBanned = false,
        ?string $avatar = null,
        ?Carbon $createdAt = null,
    ) {
        $name = trim($name);
        if (empty($name)) {
            throw new InvalidArgumentException(__('validation.required', ['attribute' => 'name']));
        }

        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->avatar = $avatar;
        $this->isVerified = $isVerified;
        $this->isBanned = $isBanned;
        $this->createdAt = $createdAt;
    }

    // public static function fromModel(User $user): self
    // {
    //     return new self(
    //         id: $user->id,
    //         name: $user->name,
    //         email: $user->email,
    //         isVerified: $user->email_verified_at !== null,
    //         isBanned: $user->banned_at !== null,
    //         avatar: $user->avatar
    //             ? str_replace('{id}', $user->id, $user->avatar)
    //             : 'https://placehold.co/80x80.png?text=Foto',
    //         created_at: $user->created_at
    //             ->timezone(config('app.timezone'))
    //             ->toIso8601String(),
    //         roles: $user->getRoleNames()->toArray(),
    //     );
    // }
}
