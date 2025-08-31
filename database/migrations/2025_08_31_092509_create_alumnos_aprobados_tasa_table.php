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
        Schema::create('alumnos_aprobados_tasa', function (Blueprint $table) {
            $table->id();
            $table->string('alumno_dni');
            $table->foreign('alumno_dni')->references('dni')->on('alumnos')->onDelete('cascade');

            $table->unsignedBigInteger('maestria_id');
            $table->foreign('maestria_id')->references('id')->on('maestrias')->onDelete('cascade');

            $table->timestamps();

            $table->unique(['alumno_dni', 'maestria_id']); // Evita duplicados
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos_aprobados_tasa');
    }

};
