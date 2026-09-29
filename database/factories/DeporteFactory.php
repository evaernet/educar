<?php

namespace Database\Factories;

use App\Models\Deporte;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeporteFactory extends Factory
{
    protected $model = Deporte::class;

    public function definition(): array
    {
        return ['nombre' => fake()->unique()->word(), 'activo' => true];
    }
}
