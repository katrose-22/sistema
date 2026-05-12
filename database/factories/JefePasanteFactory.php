<?php

namespace Database\Factories;

use App\Models\JefePasante;
use Illuminate\Database\Eloquent\Factories\Factory;

class JefePasanteFactory extends Factory
{
    protected $model = JefePasante::class;

    public function definition(): array
    {
        return [
            'cargo' => fake()->jobTitle(),
            'telefono' => fake()->phoneNumber(),
        ];
    }
}
