<?php

namespace Database\Factories;

use App\Models\Contracts;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractsFactory extends Factory
{
    protected $model = Contracts::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->words(2, true) . ' Plan',
            'megabytes' => fake()->randomElement([10, 20, 50, 100]),
            'costo' => fake()->randomFloat(2, 50, 500),
        ];
    }
}
