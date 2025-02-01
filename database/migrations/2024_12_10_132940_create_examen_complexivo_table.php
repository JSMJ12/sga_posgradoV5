<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamenComplexivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examen_complexivo', function (Blueprint $table) {
            $table->id(); // ID único del registro
            $table->string('alumno_dni'); // DNI del alumno
            $table->string('lugar'); // Lugar del examen
            $table->timestamp('fecha_hora'); // Fecha y hora del examen
            $table->decimal('nota', 5, 2)->nullable(); // Nota del examen (puede ser nula al inicio)
            $table->timestamps(); // Timestamps para created_at y updated_at
            $table->foreign('alumno_dni')->references('dni')->on('alumnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examen_complexivo');
    }
}
