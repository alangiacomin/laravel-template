<?php

namespace App\Areas\Main\Auth\Application\Data;

use App\Areas\Main\Auth\Domain\Entities\UserItem;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScript]
class UserData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $isVerified = false,
        public readonly bool $isBanned = false,
        #[TypeScriptType('string')]
        public readonly ?string $avatar = null,
        public readonly ?string $created_at = null,
    ) {}

    public static function fromModel(UserItem $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            isVerified: $user->isVerified,
            isBanned: $user->isBanned,
            avatar: $user->avatar
                ? str_replace('{id}', "{$user->id}", $user->avatar)
                : 'https://placehold.co/80x80.png?text=Foto',
            created_at: $user->createdAt?->format('Y-m-d H:i:s'),
        );
    }
}
