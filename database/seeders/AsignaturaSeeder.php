<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asignatura;
use App\Models\Maestria;
use Illuminate\Support\Str;

class AsignaturaSeeder extends Seeder
{
    public function run()
    {
        // Obtener todas las maestrías
        $maestrias = Maestria::all();

        // Generar 10 asignaturas para cada maestría
        foreach ($maestrias as $maestria) {
            for ($i = 1; $i <= 10; $i++) {
                Asignatura::create([
                    'nombre' => 'Asignatura ' . $i . ' de ' . $maestria->nombre,
                    'codigo_asignatura' => Str::upper(Str::random(8)), // Código único aleatorio
                    'credito' => fake()->numberBetween(1, 5), // Créditos entre 1 y 5
                    'itinerario' => fake()->randomElement(['Semestral', 'Anual']),
                    'unidad_curricular' => fake()->word(),
                    'maestria_id' => $maestria->id,
                ]);
            }
        }
    }
}
