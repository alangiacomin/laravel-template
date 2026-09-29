<?php

namespace App\Areas\Admin\Fumetti\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use Illuminate\Support\Facades\DB;

class DeleteSerieCommand extends Command
{
    public function __construct(
        public int $id,
    ) {}

    public function handle(): void
    {
        DB::transaction(function (): void {
            $serie = Serie::query()->findOrFail($this->id);
            $serie->associazioni()->delete();
            $serie->delete();
        });
    }
}
