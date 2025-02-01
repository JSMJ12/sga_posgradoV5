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
        Schema::create('calificacion_verificacion', function (Blueprint $table) {
            $table->id();
            $table->string('docente_dni');
            $table->unsignedBigInteger('asignatura_id');
            $table->unsignedBigInteger('cohorte_id');
            $table->boolean('calificado')->default(false);
            $table->boolean('editar')->default(false);
            $table->timestamps();
            $table->foreign('docente_dni')->references('dni')->on('docentes')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
            $table->foreign('cohorte_id')->references('id')->on('cohortes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificacion_verificacion');
    }
};
