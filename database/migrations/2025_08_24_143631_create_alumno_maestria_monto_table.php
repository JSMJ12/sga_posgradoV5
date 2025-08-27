<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Crear la nueva tabla
        Schema::create('alumno_maestria_monto', function (Blueprint $table) {
            $table->id();

            $table->string('alumno_dni');
            $table->foreign('alumno_dni')
                  ->references('dni')
                  ->on('alumnos')
                  ->onDelete('cascade');

            $table->foreignId('maestria_id')->constrained()->onDelete('cascade');

            $table->decimal('monto_arancel', 12, 2)->default(0);
            $table->decimal('monto_matricula', 12, 2)->default(0);
            $table->decimal('monto_inscripcion', 12, 2)->default(0);

            $table->timestamps();
        });

        // 2️⃣ Migrar datos actuales tomando los montos de cada maestría
        $alumnos = DB::table('alumnos')->get();

        foreach ($alumnos as $alumno) {
            // Obtener las maestrías del alumno desde la tabla pivote alumno_maestria
            $maestrias = DB::table('alumnos_maestrias')->where('alumno_dni', $alumno->dni)->get();

            foreach ($maestrias as $maestria) {
                $maestriaData = DB::table('maestrias')->where('id', $maestria->maestria_id)->first();

                if ($maestriaData) {
                    DB::table('alumno_maestria_monto')->insert([
                        'alumno_dni' => $alumno->dni,
                        'maestria_id' => $maestria->maestria_id,
                        'monto_arancel' => $maestriaData->arancel ?? 0,
                        'monto_matricula' => $maestriaData->matricula ?? 0,
                        'monto_inscripcion' => $maestriaData->inscripcion ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3️⃣ Eliminar columnas antiguas de alumnos
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn(['monto_total', 'monto_matricula', 'monto_inscripcion']);
        });
    }

    public function down(): void
    {
        // Restaurar columnas antiguas de alumnos
        Schema::table('alumnos', function (Blueprint $table) {
            $table->decimal('monto_total', 12, 2)->default(0);
            $table->decimal('monto_matricula', 12, 2)->default(0);
            $table->decimal('monto_inscripcion', 12, 2)->default(0);
        });

        Schema::dropIfExists('alumno_maestria_monto');
    }
};
