<?php

namespace Tests\Feature\Fumetti;

use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ComicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_is_public_and_localized(): void
    {
        $this->get('/it/fumetti')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('App/Comics/Comics', false)
                ->where('comics.total', 0)
                ->has('testate', 0)
                ->has('serie', 0));

        $this->get('/en/comics')->assertOk();
    }

    public function test_search_and_filters_find_comics_by_editorial_metadata_without_duplicate_issues(): void
    {
        $testata = Testata::factory()->create(['titolo' => 'Diabolik']);
        $serie = Serie::factory()->create(['testata_id' => $testata->id, 'titolo' => 'Inedita']);
        $albo = Albo::factory()->create(['titolo' => 'Il re del terrore']);
        $albo->associazioni()->create([
            'serie_id' => $serie->id,
            'numero' => 1,
            'data_pubblicazione' => '2024-05-10',
        ]);
        $altraSerie = Serie::factory()->create(['testata_id' => $testata->id, 'titolo' => 'Ristampa']);
        $albo->associazioni()->create([
            'serie_id' => $altraSerie->id,
            'numero' => 5,
            'data_pubblicazione' => '2025-02-01',
        ]);
        $altroAlbo = Albo::factory()->create(['titolo' => 'La valle degli agguati']);
        $altroAlbo->associazioni()->create([
            'serie_id' => $serie->id,
            'numero' => 2,
            'data_pubblicazione' => '2023-02-01',
        ]);

        $this->get('/it/fumetti?q=Diabolik&testata_id='.$testata->id.'&serie_id='.$serie->id.'&year=2024')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.q', 'Diabolik')
                ->where('filters.testata_id', $testata->id)
                ->where('filters.serie_id', $serie->id)
                ->where('filters.year', 2024)
                ->where('comics.total', 1)
                ->where('comics.data.0.id', $albo->id)
                ->where('comics.data.0.titolo', 'Il re del terrore')
                ->has('comics.data.0.pubblicazioni', 1)
                ->where('comics.data.0.pubblicazioni.0.testata', 'Diabolik')
                ->where('comics.data.0.pubblicazioni.0.serie', 'Inedita')
                ->where('comics.data.0.pubblicazioni.0.dataPubblicazione', '2024-05-10'));

        $this->get('/it/fumetti?q=Il+re+del+terrore')
            ->assertInertia(fn (Assert $page) => $page
                ->where('comics.total', 1)
                ->where('comics.data.0.id', $albo->id)
                ->has('comics.data.0.pubblicazioni', 2));

        $this->get('/it/fumetti?year=2023')
            ->assertInertia(fn (Assert $page) => $page
                ->where('comics.total', 1)
                ->where('comics.data.0.id', $altroAlbo->id));
    }

    public function test_catalog_paginates_results(): void
    {
        Albo::factory()->count(19)->create();

        $this->get('/it/fumetti')
            ->assertInertia(fn (Assert $page) => $page
                ->where('comics.total', 19)
                ->where('comics.current_page', 1)
                ->where('comics.last_page', 2)
                ->has('comics.data', 18)
                ->has('comics.next_page_url'));
    }
}
