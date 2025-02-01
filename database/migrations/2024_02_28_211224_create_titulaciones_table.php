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
        Schema::create('titulaciones', function (Blueprint $table) {
            $table->id();
            $table->string('alumno_dni'); // La columna 'alumno_id' como clave foránea referenciando a la tabla 'alumnos'
            $table->boolean('titulado')->default(false); // Columna para manejar si el estudiante ya se tituló
            $table->string('tesis_path')->nullable(); // Columna para almacenar la ruta del archivo de la tesis, nullable para permitir valores nulos
            $table->timestamps();
            $table->foreign('alumno_dni')->references('dni')->on('alumnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titulaciones');
    }
};
