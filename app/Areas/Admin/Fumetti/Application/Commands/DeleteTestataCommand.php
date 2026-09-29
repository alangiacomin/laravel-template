<?php

namespace App\Areas\Admin\Fumetti\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Pubblicazioni;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Illuminate\Support\Facades\DB;

class DeleteTestataCommand extends Command
{
    public function __construct(
        public int $id,
    ) {}

    public function handle(): void
    {
        DB::transaction(function (): void {
            $testata = Testata::query()->findOrFail($this->id);
            $serie = $testata->serie;
            $serieIds = $serie->modelKeys();

            if ($serieIds !== []) {
                Pubblicazioni::query()->whereIn('serie_id', $serieIds)->delete();
                $serie->each->delete();
            }

            $testata->delete();
        });
    }
}
