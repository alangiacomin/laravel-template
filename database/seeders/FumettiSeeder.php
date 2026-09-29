<?php

namespace Database\Seeders;

use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Pubblicazioni;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Illuminate\Database\Seeder;

class FumettiSeeder extends Seeder
{
    public function run(): void
    {
        $tex = Testata::firstOrCreate([
            'titolo' => 'Tex',
        ]);

        $diabolik = Testata::firstOrCreate([
            'titolo' => 'Diabolik',
        ]);

        $texInedita = Serie::firstOrCreate([
            'titolo' => 'Inedita',
        ]);

        $inedita = Serie::firstOrCreate([
            'titolo' => 'Inedita',
        ]);

        $swiss = Serie::firstOrCreate([
            'titolo' => 'Swiss',
        ]);

        $ristampa = Serie::firstOrCreate([
            'titolo' => 'Ristampa',
        ]);

        Serie::firstOrCreate([
            'titolo' => 'Maxi',
        ]);

        $diabolikAlbo = Albo::firstOrCreate(
            [
                'titolo' => 'Il re del terrore',
                'testata_id' => $diabolik->id,
            ],
        );

        foreach ([
            $inedita->id => [
                'numero' => 120,
                'numero_gruppo' => null,
                'data_pubblicazione' => '2022-01-15',
            ],
            $swiss->id => [
                'numero' => 45,
                'numero_gruppo' => null,
                'data_pubblicazione' => '2023-03-20',
            ],
            $ristampa->id => [
                'numero' => 12,
                'numero_gruppo' => 2024,
                'data_pubblicazione' => '2024-06-10',
            ],
        ] as $serieId => $values) {
            Pubblicazioni::query()->updateOrCreate(
                [
                    'albo_id' => $diabolikAlbo->id,
                    'serie_id' => $serieId,
                ],
                $values,
            );
        }

        $texAlbo = Albo::firstOrCreate(
            [
                'titolo' => 'La valle degli agguati',
                'testata_id' => $tex->id,
            ],
        );

        Pubblicazioni::query()->updateOrCreate(
            [
                'albo_id' => $texAlbo->id,
                'serie_id' => $texInedita->id,
            ],
            [
                'numero' => 760,
                'numero_gruppo' => null,
                'data_pubblicazione' => '2024-02-12',
            ],
        );

        Albo::firstOrCreate([
            'titolo' => 'Titolo da associare',
            'testata_id' => $diabolik->id,
        ]);
    }
}
