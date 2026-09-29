<?php

namespace App\Areas\Admin\Fumetti\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Admin\Fumetti\Application\Data\SerieInputData;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;

class UpdateSerieCommand extends Command
{
    public function __construct(
        public int $id,
        public SerieInputData $data,
    ) {}

    public function handle(): void
    {
        Serie::query()->findOrFail($this->id)->update([
            'testata_id' => $this->data->testata_id,
            'titolo' => $this->data->titolo,
        ]);
    }
}
