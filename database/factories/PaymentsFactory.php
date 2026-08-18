<?php

namespace Database\Factories;

use App\Models\Payments;
use App\Models\Client;
use App\Models\Quota;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentsFactory extends Factory
{
    protected $model = Payments::class;

    public function definition(): array
    {
        return [
            'id_cliente' => Client::factory(),
            'id_cuota' => Quota::factory(),
            'num_cuotas' => fake()->numberBetween(1, 12),
            'costo' => fake()->randomFloat(2, 50, 500),
            'abonado' => 0,
            'estado' => 0,
            'fecha_pago' => null,
            'comentario' => null,
            'image' => null,
            'image2' => null,
        ];
    }
}
