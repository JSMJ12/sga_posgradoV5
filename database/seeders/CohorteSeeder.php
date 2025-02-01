<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cohorte;
use App\Models\Aula;
use App\Models\Maestria;
use App\Models\PeriodoAcademico;
use Carbon\Carbon;

class CohorteSeeder extends Seeder
{
    public function run()
    {
        // Crear cohortes con modalidades
        foreach (range(1, 25) as $index) {
            // Seleccionar modalidad aleatoria
            $modalidad = fake()->randomElement(['Presencial', 'Híbrida', 'Virtual']);

            // Obtener una maestría y un periodo académico aleatorios
            $maestria = Maestria::inRandomOrder()->first();
            $periodoAcademico = PeriodoAcademico::inRandomOrder()->first();

            // Si la modalidad es virtual, no asignar aula
            $aula = null;
            if ($modalidad != 'Virtual') {
                $aula = Aula::inRandomOrder()->first();
            }

            Cohorte::create([
                'nombre' => 'Cohorte ' . $index,
                'maestria_id' => $maestria->id,
                'periodo_academico_id' => $periodoAcademico->id,
                'aula_id' => $aula ? $aula->id : null,
                'aforo' => fake()->numberBetween(10, 40), // Aforo aleatorio
                'modalidad' => $modalidad,
                'fecha_inicio' => Carbon::now()->addMonths($index)->startOfMonth(),
                'fecha_fin' => Carbon::now()->addMonths($index + 1)->endOfMonth(),
            ]);
        }
    }
}
