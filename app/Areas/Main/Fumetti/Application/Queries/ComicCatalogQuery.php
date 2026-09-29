<?php

namespace App\Areas\Main\Fumetti\Application\Queries;

use App\Areas\Main\Fumetti\Application\Data\CatalogComicData;
use App\Areas\Main\Fumetti\Application\Data\CatalogComicPublicationData;
use App\Areas\Main\Fumetti\Application\Data\CatalogOptionData;
use App\Areas\Main\Fumetti\Application\Data\CatalogSerieOptionData;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Pubblicazioni;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ComicCatalogQuery
{
    /**
     * @param array{
     *     q?: string|null,
     *     testata_id?: int|null,
     *     serie_id?: int|null,
     *     year?: int|null
     * } $filters
     * @return array{
     *     comics: LengthAwarePaginator,
     *     testate: list<CatalogOptionData>,
     *     serie: list<CatalogSerieOptionData>,
     *     years: list<int>
     * }
     */
    public function execute(array $filters): array
    {
        $query = Albo::query();

        $this->applyFilters($query, $filters);

        $query
            ->with([
                'testata:id,titolo',
                'pubblicazioni' => function (Relation $query) use ($filters): void {
                    $this->applyPublicationFilters($query, $filters);

                    $query
                        ->with('serie:id,titolo')
                        ->orderBy('serie_id')
                        ->orderBy('numero');
                },
            ])
            ->orderBy('titolo')
            ->orderBy('id');

        $comics = $query
            ->paginate(18)
            ->withQueryString()
            ->through(
                fn (Albo $albo): array => new CatalogComicData(
                    id: $albo->id,
                    titolo: $albo->titolo,
                    testata: new CatalogOptionData(
                        $albo->testata->id,
                        $albo->testata->titolo,
                    ),
                    pubblicazioni: $albo->pubblicazioni
                        ->map(
                            fn (Pubblicazioni $publication): CatalogComicPublicationData => new CatalogComicPublicationData(
                                serie: new CatalogSerieOptionData(
                                    $publication->serie->id,
                                    $publication->serie->titolo,
                                ),
                                numero: $publication->numero,
                                numeroGruppo: $publication->numero_gruppo,
                                dataPubblicazione: $publication->getRawOriginal('data_pubblicazione') === null
                                    ? null
                                    : Carbon::parse(
                                        (string) $publication->getRawOriginal('data_pubblicazione'),
                                    )->toDateString(),
                            ),
                        )
                        ->all(),
                )->toArray(),
            );

        return [
            'comics' => $comics,

            'testate' => Testata::query()
                ->whereHas('albi.pubblicazioni')
                ->orderBy('titolo')
                ->get(['id', 'titolo'])
                ->map(
                    fn (Testata $testata): CatalogOptionData => new CatalogOptionData(
                        $testata->id,
                        $testata->titolo,
                    ),
                )
                ->all(),

            'serie' => Serie::query()
                ->whereHas('pubblicazioni')
                ->orderBy('titolo')
                ->get(['id', 'titolo'])
                ->map(
                    fn (Serie $serie): CatalogSerieOptionData => new CatalogSerieOptionData(
                        $serie->id,
                        $serie->titolo,
                    ),
                )
                ->all(),

            'years' => Pubblicazioni::query()
                ->whereNotNull('data_pubblicazione')
                ->pluck('data_pubblicazione')
                ->map(
                    fn (mixed $date): int => (int) Carbon::parse((string) $date)->format('Y'),
                )
                ->unique()
                ->sortDesc()
                ->values()
                ->all(),
        ];
    }

    /**
     * @param array{
     *     q?: string|null,
     *     testata_id?: int|null,
     *     serie_id?: int|null,
     *     year?: int|null
     * } $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        $query->when(
            isset($filters['testata_id']),
            fn (Builder $query) => $query->where(
                'testata_id',
                $filters['testata_id'],
            ),
        );

        $query->when(
            isset($filters['serie_id']) || isset($filters['year']),
            fn (Builder $query) => $query->whereHas(
                'pubblicazioni',
                fn (Builder $query) => $this->applyPublicationFilters(
                    $query,
                    $filters,
                ),
            ),
        );

        if (isset($filters['q']) && trim($filters['q']) !== '') {
            $search = trim($filters['q']);

            $query->where(function (Builder $query) use ($search): void {
                $query
                    ->where(
                        'titolo',
                        'like',
                        '%'.$search.'%',
                    )
                    ->orWhereHas(
                        'testata',
                        fn (Builder $query) => $query->where(
                            'titolo',
                            'like',
                            '%'.$search.'%',
                        ),
                    )
                    ->orWhereHas(
                        'pubblicazioni.serie',
                        fn (Builder $query) => $query->where(
                            'titolo',
                            'like',
                            '%'.$search.'%',
                        ),
                    );
            });
        }
    }

    /**
     * @param array{
     *     q?: string|null,
     *     testata_id?: int|null,
     *     serie_id?: int|null,
     *     year?: int|null
     * } $filters
     */
    private function applyPublicationFilters(
        Builder|Relation $query,
        array $filters,
    ): void {
        $query->when(
            isset($filters['serie_id']),
            fn (Builder|Relation $query) => $query->where(
                'serie_id',
                $filters['serie_id'],
            ),
        );

        $query->when(
            isset($filters['year']),
            fn (Builder|Relation $query) => $query->whereBetween(
                'data_pubblicazione',
                [
                    $filters['year'].'-01-01',
                    $filters['year'].'-12-31',
                ],
            ),
        );
    }
}
