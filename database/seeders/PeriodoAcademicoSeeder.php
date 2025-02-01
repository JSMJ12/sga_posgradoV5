<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PeriodoAcademico;
use Carbon\Carbon;

class PeriodoAcademicoSeeder extends Seeder
{
    public function run()
    {
        // Crear 5 periodos académicos con fechas aleatorias
        foreach (range(1, 5) as $index) {
            PeriodoAcademico::create([
                'nombre' => 'Periodo ' . $index . ' ' . Carbon::now()->year,
                'fecha_inicio' => Carbon::now()->addMonths($index)->startOfMonth(),
                'fecha_fin' => Carbon::now()->addMonths($index + 1)->endOfMonth(),
            ]);
        }
    }
}
