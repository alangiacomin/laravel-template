<?php

namespace App\Areas\Admin\Users\Application\Jobs;

use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;

class BloccaUtenteJob
{
    use Dispatchable;

    public function __construct(
        public int $id,
    ) {}

    public function handle(): void
    {
        User::findOrFail($this->id)->ban();
    }
}
