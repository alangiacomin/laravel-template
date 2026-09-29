<?php

namespace Database\Factories;

use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Albo>
 */
class AlboFactory extends Factory
{
    protected $model = Albo::class;

    public function definition(): array
    {
        return [
            'titolo' => fake()->sentence(4),
            'testata_id' => Testata::factory(),
        ];
    }
}
