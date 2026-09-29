<?php

namespace Database\Factories;

use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testata>
 */
class TestataFactory extends Factory
{
    protected $model = Testata::class;

    public function definition(): array
    {
        return [
            'titolo' => fake()->unique()->company(),
        ];
    }
}
