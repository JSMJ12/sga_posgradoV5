<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aula;

class AulaSeeder extends Seeder
{
    public function run()
    {
        // Crear 25 aulas con datos aleatorios
        foreach (range(1, 25) as $index) {
            Aula::create([
                'nombre' => 'Aula ' . $index,
                'piso' => fake()->numberBetween(1, 5), // Piso aleatorio entre 1 y 5
                'codigo' => 'A' . str_pad($index, 3, '0', STR_PAD_LEFT), // Código de aula A001, A002, ...
                'paralelo' => fake()->randomElement(['A', 'B', 'C', 'D']), // Paralelo aleatorio
            ]);
        }
    }
}
