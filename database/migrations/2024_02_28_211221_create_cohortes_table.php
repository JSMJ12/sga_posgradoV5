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
        Schema::create('cohortes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('maestria_id');
            $table->unsignedBigInteger('periodo_academico_id');
            $table->unsignedBigInteger('aula_id')->nullable();
            $table->integer('aforo');
            $table->enum('modalidad', ['presencial', 'hibrida', 'virtual']);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamps();

            $table->foreign('maestria_id')->references('id')->on('maestrias')->onDelete('cascade');
            $table->foreign('periodo_academico_id')->references('id')->on('periodos_academicos')->onDelete('cascade');
            $table->foreign('aula_id')->references('id')->on('aulas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cohortes');
    }
};
