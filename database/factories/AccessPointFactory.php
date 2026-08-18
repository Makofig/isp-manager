<?php

namespace Database\Factories;

use App\Models\Access_Point;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessPointFactory extends Factory
{
    protected $model = Access_Point::class;

    public function definition(): array
    {
        return [
            'ssid' => fake()->word() . '_WiFi',
            'frecuencia' => fake()->randomElement(['2.4GHz', '5GHz']),
            'ip_ap' => fake()->localIpv4(),
            'localidad' => fake()->city(),
        ];
    }
}
