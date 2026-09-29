<?php

namespace Tests\Feature\Fumetti;

use App\Areas\Admin\Fumetti\Application\Commands\DeleteTestataCommand;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DominioFumettiTest extends TestCase
{
    use RefreshDatabase;

    public function test_una_testata_puo_esistere_senza_serie(): void
    {
        $testata = Testata::factory()->create();

        $this->assertCount(0, $testata->serie);
    }

    public function test_una_serie_richiede_una_testata(): void
    {
        $serie = Serie::factory()->create();

        $this->assertTrue($serie->testata->exists);
    }

    public function test_un_albo_puo_esistere_senza_serie(): void
    {
        $albo = Albo::factory()->create();

        $this->assertCount(0, $albo->associazioni);
    }

    public function test_un_albo_puo_appartenere_a_piu_serie_con_dati_indipendenti(): void
    {
        $testata = Testata::factory()->create();
        $inedita = Serie::factory()->create(['testata_id' => $testata->id, 'titolo' => 'Inedita']);
        $ristampa = Serie::factory()->create(['testata_id' => $testata->id, 'titolo' => 'Ristampa']);
        $albo = Albo::factory()->create(['titolo' => 'Il re del terrore']);

        $ineditaAssociazione = $albo->associazioni()->create([
            'serie_id' => $inedita->id,
            'numero' => 120,
            'data_pubblicazione' => '2024-01-15',
        ]);
        $ristampaAssociazione = $albo->associazioni()->create([
            'serie_id' => $ristampa->id,
            'numero' => 12,
            'numero_gruppo' => 2024,
            'data_pubblicazione' => null,
        ]);

        $this->assertSame('Il re del terrore', $albo->fresh()->titolo);
        $this->assertSame(120, $ineditaAssociazione->fresh()->numero);
        $this->assertSame(12, $ristampaAssociazione->fresh()->numero);
        $this->assertSame(2024, $ristampaAssociazione->fresh()->numero_gruppo);
        $this->assertNull($ristampaAssociazione->fresh()->data_pubblicazione);
    }

    public function test_eliminare_una_testata_elimina_logicamente_serie_e_associazioni(): void
    {
        $testata = Testata::factory()->create();
        $serie = Serie::factory()->create(['testata_id' => $testata->id]);
        $albo = Albo::factory()->create();
        $associazione = $albo->associazioni()->create([
            'serie_id' => $serie->id,
            'numero' => 1,
        ]);

        (new DeleteTestataCommand($testata->id))->handle();

        $this->assertSoftDeleted('testate', ['id' => $testata->id]);
        $this->assertSoftDeleted('serie', ['id' => $serie->id]);
        $this->assertSoftDeleted('albo_serie', ['id' => $associazione->id]);
        $this->assertDatabaseHas('albi', ['id' => $albo->id, 'deleted_at' => null]);
    }
}
