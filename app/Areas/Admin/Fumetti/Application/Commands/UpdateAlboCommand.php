<?php

namespace App\Areas\Admin\Fumetti\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Admin\Fumetti\Application\Data\AlboInputData;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Pubblicazioni;
use Illuminate\Support\Facades\DB;

class UpdateAlboCommand extends Command
{
    public function __construct(
        public int $id,
        public AlboInputData $data,
    ) {}

    public function handle(): void
    {
        DB::transaction(function (): void {
            $albo = Albo::query()->findOrFail($this->id);
            $albo->update(['titolo' => $this->data->titolo]);
            $serieIds = [];

            foreach ($this->data->serie as $data) {
                $serieIds[] = $data->serie_id;
                $values = [
                    'numero' => $data->numero,
                    'numero_gruppo' => $data->numero_gruppo,
                    'data_pubblicazione' => $data->data_pubblicazione,
                ];
                $associazione = Pubblicazioni::withTrashed()
                                             ->where('albo_id', $albo->id)
                                             ->where('serie_id', $data->serie_id)
                                             ->latest('id')
                                             ->first();

                if ($associazione === null) {
                    $albo->associazioni()->create(['serie_id' => $data->serie_id, ...$values]);
                } else {
                    $associazione->fill($values);
                    $associazione->restore();
                    $associazione->save();
                }
            }

            $albo->associazioni()->when(
                $serieIds === [],
                fn ($query) => $query,
                fn ($query) => $query->whereNotIn('serie_id', $serieIds),
            )->delete();
        });
    }
}
