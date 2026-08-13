<?php

namespace App\Areas\Admin\Users\Application\Commands;

use App\Areas\Main\Auth\Domain\Repositories\IUserRepository;
use AlanGiacomin\LaravelCqrs\App\Application\Commands\SyncCommand;

class SbloccaUtenteCommand extends SyncCommand
{
    public function __construct(
        public int $id,
    ) {}

    public function handle(IUserRepository $repo): void
    {
        $repo->update(
            $this->id,
            ['banned_at' => null],
        );
    }

    public function getResponse(): null
    {
        return null;
    }
}
