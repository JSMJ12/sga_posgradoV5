<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear la tabla pivote
        Schema::create('alumnos_maestrias', function (Blueprint $table) {
            $table->id();
            $table->string('alumno_dni'); // PK de alumnos
            $table->unsignedBigInteger('maestria_id'); // PK de maestrias
            $table->timestamps();

            $table->foreign('alumno_dni')->references('dni')->on('alumnos')->onDelete('cascade');
            $table->foreign('maestria_id')->references('id')->on('maestrias')->onDelete('cascade');
        });

        // 2. Migrar los datos actuales de alumnos.maestria_id → alumnos_maestrias
        DB::statement("
            INSERT INTO alumnos_maestrias (alumno_dni, maestria_id, created_at, updated_at)
            SELECT dni, maestria_id, NOW(), NOW()
            FROM alumnos
            WHERE maestria_id IS NOT NULL
        ");

        // 3. Eliminar la columna maestria_id de alumnos
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropForeign(['maestria_id']);
            $table->dropColumn('maestria_id');
        });
    }

    public function down(): void
    {
        // Restaurar columna si se hace rollback
        Schema::table('alumnos', function (Blueprint $table) {
            $table->unsignedBigInteger('maestria_id')->nullable();
            $table->foreign('maestria_id')->references('id')->on('maestrias')->onDelete('cascade');
        });

        Schema::dropIfExists('alumnos_maestrias');
    }
};
