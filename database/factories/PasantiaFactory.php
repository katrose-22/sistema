<?php

namespace Database\Factories;

use App\Models\Pasantia;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasantiaFactory extends Factory
{
    protected $model = Pasantia::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->catchPhrase(),
            'descripcion' => fake()->paragraph(),
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addMonths(3),
            'estado' => 'activo',
            'horario' => '8:00 AM - 5:00 PM',
        ];
    }
}
