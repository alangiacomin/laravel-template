<?php

namespace App\Areas\Admin\Fumetti\Application\Queries;

use App\Areas\Admin\Fumetti\Application\Data\AlboDetailData;
use App\Areas\Admin\Fumetti\Application\Data\AlboDetailPubblicazioneData;
use App\Areas\Admin\Fumetti\Application\Data\AlboOptionData;
use App\Areas\Admin\Fumetti\Application\Data\AlboSummaryData;
use App\Areas\Admin\Fumetti\Application\Data\AlboSummaryPubblicazioneData;
use App\Areas\Admin\Fumetti\Application\Data\SerieDetailData;
use App\Areas\Admin\Fumetti\Application\Data\SerieOptionData;
use App\Areas\Admin\Fumetti\Application\Data\SerieSummaryData;
use App\Areas\Admin\Fumetti\Application\Data\TestataDetailData;
use App\Areas\Admin\Fumetti\Application\Data\TestataNameData;
use App\Areas\Admin\Fumetti\Application\Data\TestataOptionData;
use App\Areas\Admin\Fumetti\Application\Data\TestataSummaryData;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Pubblicazioni;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class FumettiPageQueries
{
    /**
     * @return list<TestataSummaryData>
     */
    public function testate(): array
    {
        return Testata::query()
            ->with([
                'albi' => fn (Relation $query) => $query->orderBy('titolo'),
            ])
            ->withCount('albi')
            ->orderBy('titolo')
            ->get()
            ->map(
                fn (Testata $testata): TestataSummaryData => new TestataSummaryData(
                    $testata->id,
                    $testata->titolo,
                    $testata->albi_count,
                    $testata->albi
                        ->map(
                            fn (Albo $albo): AlboOptionData => new AlboOptionData(
                                $albo->id,
                                $albo->titolo,
                            ),
                        )
                        ->all(),
                ),
            )
            ->all();
    }

    public function testata(int $id): TestataDetailData
    {
        $testata = Testata::query()
            ->with([
                'albi' => fn (Relation $query) => $query->orderBy('titolo'),
            ])
            ->findOrFail($id);

        return new TestataDetailData(
            $testata->id,
            $testata->titolo,
            $testata->albi
                ->map(
                    fn (Albo $albo): AlboOptionData => new AlboOptionData(
                        $albo->id,
                        $albo->titolo,
                    ),
                )
                ->all(),
        );
    }

    /**
     * @return list<SerieSummaryData>
     */
    public function serie(): array
    {
        return Serie::query()
            ->withCount('pubblicazioni')
            ->orderBy('titolo')
            ->get()
            ->map(
                fn (Serie $serie): SerieSummaryData => new SerieSummaryData(
                    $serie->id,
                    $serie->titolo,
                    $serie->pubblicazioni_count,
                ),
            )
            ->all();
    }

    public function serieDettaglio(int $id): SerieDetailData
    {
        $serie = Serie::query()
            ->with([
                'pubblicazioni.albo' => fn (Relation $query) => $query->orderBy('titolo'),
            ])
            ->findOrFail($id);

        return new SerieDetailData(
            $serie->id,
            $serie->titolo,
            $serie->pubblicazioni
                ->map(
                    fn (Pubblicazioni $pubblicazione): AlboOptionData => new AlboOptionData(
                        $pubblicazione->albo->id,
                        $pubblicazione->albo->titolo,
                    ),
                )
                ->unique('id')
                ->values()
                ->all(),
        );
    }

    /**
     * @return list<AlboSummaryData>
     */
    public function albi(
        ?int $testataId = null,
        ?int $serieId = null,
    ): array {
        $query = Albo::query()
            ->with([
                'testata:id,titolo',
                'pubblicazioni' => function (Relation $query) use ($serieId): void {
                    if ($serieId !== null) {
                        $query->where('serie_id', $serieId);
                    }
                },
                'pubblicazioni.serie:id,titolo',
            ]);

        if ($testataId !== null) {
            $query->where('testata_id', $testataId);
        }

        if ($serieId !== null) {
            $query->whereHas(
                'pubblicazioni',
                fn (Builder $query) => $query->where(
                    'serie_id',
                    $serieId,
                ),
            );
        }

        return $query
            ->orderBy('titolo')
            ->get()
            ->map(
                fn (Albo $albo): AlboSummaryData => new AlboSummaryData(
                    $albo->id,
                    $albo->titolo,
                    new TestataOptionData(
                        $albo->testata->id,
                        $albo->testata->titolo,
                    ),
                    $albo->pubblicazioni
                        ->map(
                            fn (Pubblicazioni $pubblicazione): AlboSummaryPubblicazioneData => new AlboSummaryPubblicazioneData(
                                $pubblicazione->serie->id,
                                $pubblicazione->serie->titolo,
                                $pubblicazione->numero,
                                $pubblicazione->numero_gruppo,
                                $this->publicationDate($pubblicazione),
                            ),
                        )
                        ->all(),
                ),
            )
            ->all();
    }

    public function albo(int $id): AlboDetailData
    {
        $albo = Albo::query()
            ->with([
                'testata:id,titolo',
                'pubblicazioni.serie:id,titolo',
            ])
            ->findOrFail($id);

        return new AlboDetailData(
            $albo->id,
            $albo->titolo,
            new TestataNameData(
                $albo->testata->titolo,
            ),
            $albo->pubblicazioni
                ->map(
                    function (Pubblicazioni $pubblicazione): AlboDetailPubblicazioneData {
                        return new AlboDetailPubblicazioneData(
                            $pubblicazione->serie->id,
                            $pubblicazione->serie->titolo,
                            $pubblicazione->id,
                            $pubblicazione->numero,
                            $pubblicazione->numero_gruppo,
                            $this->publicationDate($pubblicazione),
                        );
                    },
                )
                ->all(),
        );
    }

    /**
     * @return list<TestataOptionData>
     */
    public function testateOptions(): array
    {
        return Testata::query()
            ->orderBy('titolo')
            ->get(['id', 'titolo'])
            ->map(
                fn (Testata $testata): TestataOptionData => new TestataOptionData(
                    $testata->id,
                    $testata->titolo,
                ),
            )
            ->all();
    }

    /**
     * @return list<SerieOptionData>
     */
    public function serieOptions(): array
    {
        return Serie::query()
            ->orderBy('titolo')
            ->get(['id', 'titolo'])
            ->map(
                fn (Serie $serie): SerieOptionData => new SerieOptionData(
                    $serie->id,
                    $serie->titolo,
                ),
            )
            ->all();
    }

    private function publicationDate(
        Pubblicazioni $pubblicazione,
    ): ?string {
        $date = $pubblicazione->getRawOriginal('data_pubblicazione');

        return $date === null
            ? null
            : Carbon::parse((string) $date)->toDateString();
    }
}
