<?php

namespace Database\Factories;

use App\Models\Access_Point;
use App\Models\Client;
use App\Models\Contracts;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'id_plan' => Contracts::factory(),
            'id_point' => Access_Point::factory(),
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'direccion' => fake()->streetAddress(),
            'telefono' => fake()->phoneNumber(),
            'ip' => fake()->ipv4(),
            'imagen' => null,
            'is_banned' => false,
        ];
    }
}
