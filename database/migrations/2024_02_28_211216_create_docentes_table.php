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
        Schema::create('docentes', function (Blueprint $table) {
            $table->string('dni')->primary();
            $table->string('nombre1');
            $table->string('nombre2');
            $table->string('apellidop');
            $table->string('apellidom');
            $table->string('email');
            $table->char('contra', 255)->nullable();
            $table->char('sexo', 1);
            $table->enum('tipo', ['NOMBRADO', 'CONTRATADO'])->nullable();
            $table->enum('status', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->text('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
