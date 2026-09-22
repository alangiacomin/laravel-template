<?php

namespace App\Areas\Admin\Users\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Models\User;

class BloccaUtenteCommand extends Command
{
    public function __construct(
        public int $id,
    ) {}

    public function handle(): void
    {
        User::findOrFail($this->id)->ban();
    }
}
