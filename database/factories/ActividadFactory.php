<?php

namespace Database\Factories;

use App\Models\Actividad;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    public function definition(): array
    {
        return [
            'titulo' => fake()->catchPhrase(),
            'descripcion' => fake()->paragraph(),
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(7),
            'avance' => 0,
        ];
    }
}
