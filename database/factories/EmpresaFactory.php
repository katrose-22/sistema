<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'nit' => fake()->unique()->numerify('##########'),
        ];
    }
}
