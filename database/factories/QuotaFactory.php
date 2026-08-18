<?php

namespace Database\Factories;

use App\Models\Quota;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotaFactory extends Factory
{
    protected $model = Quota::class;

    public function definition(): array
    {
        return [
            'numero' => fake()->unique()->numberBetween(1, 12),
        ];
    }
}
