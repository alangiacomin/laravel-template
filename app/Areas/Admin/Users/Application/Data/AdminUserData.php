<?php

namespace App\Areas\Admin\Users\Application\Data;

use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScript]
class AdminUserData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $isVerified,
        public readonly bool $isBanned,
        #[TypeScriptType('string')]
        public readonly ?string $avatar = null,
        public readonly ?string $created_at = null,
        public readonly array $roles = [],
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            isVerified: $user->hasVerifiedEmail(),
            isBanned: $user->isBanned(),
            avatar: $user->avatar
                ? str_replace('{id}', "{$user->id}", $user->avatar)
                : 'https://placehold.co/80x80.png?text=Foto',
            created_at: $user->created_at?->format('Y-m-d H:i:s'),
            roles: $user->getRoleNames()->toArray(),
        );
    }
}
