<?php

namespace Tests\Feature\Fumetti;

use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Database\Seeders\FumettiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FumettiSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(FumettiSeeder::class);
        $this->seed(FumettiSeeder::class);

        $this->assertSame(2, Testata::count());
        $this->assertSame(5, Serie::count());
        $this->assertSame(3, Albo::count());
        $this->assertSame(4, Albo::query()->withCount('associazioni')->get()->sum('associazioni_count'));
    }
}
