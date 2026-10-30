<?php

namespace App\Areas\Admin\Users\Application\Jobs;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BlockUserJob
{
    use Dispatchable;

    public function __construct(
        public int $id,
        public int $actorId,
    ) {}

    public function handle(): void
    {
        $target = User::findOrFail($this->id);

        if ($target->id === $this->actorId) {
            throw new HttpException(403, __('admin.user_block_self_forbidden'));
        }

        if ($target->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            throw new HttpException(403, __('admin.user_block_super_admin_forbidden'));
        }

        $target->ban();
    }
}
