<?php

namespace App\Areas\Admin\Fumetti\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Admin\Fumetti\Application\Data\AlboInputData;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use Illuminate\Support\Facades\DB;

class CreateAlboCommand extends Command
{
    public function __construct(
        public AlboInputData $data,
    ) {}

    public function handle(): void
    {
        DB::transaction(function (): void {
            $albo = Albo::query()->create(['titolo' => $this->data->titolo]);

            foreach ($this->data->serie as $data) {
                $albo->associazioni()->create([
                    'serie_id' => $data->serie_id,
                    'numero' => $data->numero,
                    'numero_gruppo' => $data->numero_gruppo,
                    'data_pubblicazione' => $data->data_pubblicazione,
                ]);
            }
        });
    }
}
