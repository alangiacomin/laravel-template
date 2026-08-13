<?php

namespace App\Areas\Admin\Users\Application\Commands;

use App\Areas\Main\Auth\Domain\Repositories\IUserRepository;
use AlanGiacomin\LaravelCqrs\App\Application\Commands\SyncCommand;
use Illuminate\Support\Carbon;

class BloccaUtenteCommand extends SyncCommand
{
    public function __construct(
        public int $id,
    ) {}

    public function handle(IUserRepository $repo): void
    {
        $repo->update(
            $this->id,
            ['banned_at' => Carbon::now()],
        );
    }

    public function getResponse(): null
    {
        return null;
    }
}
