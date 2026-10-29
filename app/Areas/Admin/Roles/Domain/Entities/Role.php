<?php

namespace App\Areas\Admin\Roles\Domain\Entities;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScript]
class Role extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        #[TypeScriptType('string[]')]
        public array $permissions = [],
    ) {}

    public static function fromModel(\Spatie\Permission\Models\Role $role): self
    {
        return new self(
            id: $role->id,
            name: $role->name,
            permissions: $role->permissions->pluck('name')->toArray(),
        );
    }
}
