<?php

namespace App\Areas\Admin\Fumetti\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use Illuminate\Support\Facades\DB;

class DeleteAlboCommand extends Command
{
    public function __construct(
        public int $id,
    ) {}

    public function handle(): void
    {
        DB::transaction(function (): void {
            $albo = Albo::query()->findOrFail($this->id);
            $albo->associazioni()->delete();
            $albo->delete();
        });
    }
}
