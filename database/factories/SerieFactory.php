<?php

namespace Database\Factories;

use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Serie>
 */
class SerieFactory extends Factory
{
    protected $model = Serie::class;

    public function definition(): array
    {
        return [
            'titolo' => fake()->unique()->words(2, true),
        ];
    }
}
