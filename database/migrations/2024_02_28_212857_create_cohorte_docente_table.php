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
        Schema::create('cohorte_docente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cohort_id');
            $table->string('docente_dni');
            $table->unsignedBigInteger('asignatura_id');
            $table->timestamps();
            $table->foreign('cohort_id')->references('id')->on('cohortes')->onDelete('cascade');
            $table->foreign('docente_dni')->references('dni')->on('docentes')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cohorte_docente');
    }
};
