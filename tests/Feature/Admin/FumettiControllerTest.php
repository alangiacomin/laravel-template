<?php

namespace Tests\Feature\Admin;

use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Pubblicazioni;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FumettiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_fumetti_admin(): void
    {
        $this->get('/it/admin/testate')->assertRedirect();
    }

    public function test_series_are_ordered_by_publication_then_title(): void
    {
        $user = User::factory()->create();
        $zeta = Testata::factory()->create(['titolo' => 'Zeta']);
        $alfa = Testata::factory()->create(['titolo' => 'Alfa']);
        Serie::factory()->create(['testata_id' => $zeta->id, 'titolo' => 'Alfa']);
        Serie::factory()->create(['testata_id' => $alfa->id, 'titolo' => 'Zeta']);
        Serie::factory()->create(['testata_id' => $alfa->id, 'titolo' => 'Alfa']);

        $this->actingAs($user)
            ->get('/it/admin/serie')
            ->assertInertia(fn (Assert $page) => $page
                ->where('serie.0.testata.titolo', 'Alfa')
                ->where('serie.0.titolo', 'Alfa')
                ->where('serie.1.testata.titolo', 'Alfa')
                ->where('serie.1.titolo', 'Zeta')
                ->where('serie.2.testata.titolo', 'Zeta')
                ->where('serie.2.titolo', 'Alfa'));
    }

    public function test_authenticated_user_can_manage_fumetti_and_associations(): void
    {
        $user = User::factory()->create();
        $testata = Testata::factory()->create();
        $serie = Serie::factory()->create(['testata_id' => $testata->id]);
        $albo = Albo::factory()->create();

        $this->actingAs($user)->get('/it/admin/testate')->assertOk();
        $this->actingAs($user)->post('/it/admin/testate', ['titolo' => 'Diabolik'])->assertRedirect();
        $this->actingAs($user)->post('/it/admin/albi', [
            'titolo' => 'Albo senza serie',
        ])->assertSessionHasErrors('serie');
        $this->actingAs($user)->post('/it/admin/albi', [
            'titolo' => 'Nuovo albo',
            'serie' => [[
                'serie_id' => $serie->id,
                'numero' => 2,
                'numero_gruppo' => null,
                'data_pubblicazione' => null,
            ]],
        ])->assertRedirect();
        $this->actingAs($user)->patch("/it/admin/albi/{$albo->id}/aggiorna", [
            'titolo' => $albo->titolo,
            'serie' => [[
                'serie_id' => $serie->id,
                'numero' => 1,
                'numero_gruppo' => null,
                'data_pubblicazione' => null,
            ]],
        ])->assertRedirect();

        $this->assertDatabaseHas('testate', ['titolo' => 'Diabolik']);
        $this->assertDatabaseHas('albo_serie', ['albo_id' => $albo->id, 'serie_id' => $serie->id]);
        $this->assertDatabaseHas('albi', ['titolo' => 'Nuovo albo']);
        $this->assertDatabaseHas('albo_serie', ['serie_id' => $serie->id, 'numero' => 2]);
    }

    public function test_fumetti_pages_return_explicit_typed_data_shapes(): void
    {
        $user = User::factory()->create();
        $testata = Testata::factory()->create(['titolo' => 'Diabolik']);
        $serie = Serie::factory()->create([
            'testata_id' => $testata->id,
            'titolo' => 'Inedita',
        ]);
        $albo = Albo::factory()->create(['titolo' => 'Il re del terrore']);
        $altraTestata = Testata::factory()->create(['titolo' => 'Tex']);
        $altraSerie = Serie::factory()->create([
            'testata_id' => $altraTestata->id,
            'titolo' => 'Inedita',
        ]);
        $altroAlbo = Albo::factory()->create(['titolo' => 'La valle degli agguati']);
        Pubblicazioni::query()->create([
            'albo_id' => $albo->id,
            'serie_id' => $serie->id,
            'numero' => 1,
            'numero_gruppo' => 2026,
            'data_pubblicazione' => '2026-02-14',
        ]);
        Pubblicazioni::query()->create([
            'albo_id' => $altroAlbo->id,
            'serie_id' => $altraSerie->id,
            'numero' => 12,
            'numero_gruppo' => null,
            'data_pubblicazione' => null,
        ]);

        $this->actingAs($user)
            ->get('/it/admin/testate')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Fumetti/Testate', false)
                ->has('testate.0', fn (Assert $item) => $item
                    ->where('id', $testata->id)
                    ->where('titolo', 'Diabolik')
                    ->where('serie_count', 1)
                    ->where('serie.0.id', $serie->id)
                    ->where('serie.0.titolo', 'Inedita')));

        $this->actingAs($user)
            ->get('/it/admin/serie')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Fumetti/Serie', false)
                ->where('serie.0.testata.id', $testata->id)
                ->where('serie.0.testata.titolo', 'Diabolik'));

        $this->actingAs($user)
            ->get('/it/admin/albi')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Fumetti/Albi', false)
                ->where('filters.testata_id', null)
                ->where('filters.serie_id', null)
                ->has('serie', 2)
                ->has('albi', 2));

        $this->actingAs($user)
            ->get("/it/admin/albi?testata_id={$testata->id}&serie_id={$serie->id}")
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Fumetti/Albi', false)
                ->where('filters.testata_id', $testata->id)
                ->where('filters.serie_id', $serie->id)
                ->has('serie', 1)
                ->where('serie.0.id', $serie->id)
                ->has('albi', 1)
                ->where('albi.0.id', $albo->id)
                ->where('testate.0.id', $testata->id)
                ->where('albi.0.serie.0.id', $serie->id)
                ->where('albi.0.serie.0.testata.id', $testata->id)
                ->where('albi.0.serie.0.testata.titolo', 'Diabolik')
                ->where('albi.0.serie.0.numero_gruppo', 2026)
                ->where('albi.0.serie.0.numero', 1)
                ->where('albi.0.serie.0.data_pubblicazione', '2026-02-14'));

        $this->actingAs($user)
            ->get("/it/admin/albi?serie_id={$altraSerie->id}")
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Fumetti/Albi', false)
                ->where('filters.testata_id', null)
                ->where('filters.serie_id', null)
                ->has('serie', 2)
                ->has('albi', 2));

        $this->actingAs($user)
            ->get("/it/admin/albi/{$albo->id}")
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Fumetti/Albo', false)
                ->where('albo.id', $albo->id)
                ->where('albo.serie.0.associazione.numero_gruppo', 2026)
                ->where('albo.serie.0.associazione.data_pubblicazione', '2026-02-14')
                ->has('serie.0.testata.titolo'));
    }

    public function test_removing_and_readding_a_series_soft_deletes_and_restores_the_association(): void
    {
        $user = User::factory()->create();
        $serie = Serie::factory()->create();
        $albo = Albo::factory()->create();
        $payload = [
            'titolo' => $albo->titolo,
            'serie' => [[
                'serie_id' => $serie->id,
                'numero' => 7,
                'numero_gruppo' => 2026,
                'data_pubblicazione' => null,
            ]],
        ];

        $this->actingAs($user)
            ->patch("/it/admin/albi/{$albo->id}/aggiorna", $payload)
            ->assertRedirect();

        $associazione = Pubblicazioni::query()->where('albo_id', $albo->id)->firstOrFail();

        $this->actingAs($user)
            ->patch("/it/admin/albi/{$albo->id}/aggiorna", [
                'titolo' => $albo->titolo,
                'serie' => [],
            ])
            ->assertRedirect();

        $this->assertSoftDeleted('albo_serie', ['id' => $associazione->id]);

        $this->actingAs($user)
            ->patch("/it/admin/albi/{$albo->id}/aggiorna", $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('albo_serie', [
            'id' => $associazione->id,
            'deleted_at' => null,
        ]);
        $this->assertSame(1, Pubblicazioni::withTrashed()->where('albo_id', $albo->id)->count());
    }
}
