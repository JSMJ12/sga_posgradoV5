<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohorte_id')->constrained('cohortes')->onDelete('cascade');
            $table->foreignId('asignatura_id')->constrained('asignaturas')->onDelete('cascade');
            $table->string('docente_dni', 15);
            $table->string('alumno_dni', 15);
            $table->decimal('nota_actividades', 4, 2)->nullable();
            $table->decimal('nota_practicas', 4, 2)->nullable();
            $table->decimal('nota_autonomo', 4, 2)->nullable();
            $table->decimal('examen_final', 4, 2)->nullable();
            $table->decimal('recuperacion', 4, 2)->nullable();
            $table->decimal('total', 4, 2)->nullable();
            $table->timestamps();

            $table->foreign('docente_dni')->references('dni')->on('docentes')->onDelete('cascade');
            $table->foreign('alumno_dni')->references('dni')->on('alumnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
