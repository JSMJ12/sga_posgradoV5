<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Crear la tabla pivote
        Schema::create('alumno_descuento_maestria', function (Blueprint $table) {
            $table->id();

            // Alumno identificado por DNI
            $table->string('alumno_dni');
            $table->foreign('alumno_dni')
                  ->references('dni')
                  ->on('alumnos')
                  ->onDelete('cascade');

            // Descuento
            $table->foreignId('descuento_id')->constrained()->onDelete('cascade');

            // Maestría
            $table->foreignId('maestria_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });

        Schema::table('alumnos', function (Blueprint $table) {
            if (Schema::hasColumn('alumnos', 'descuento_id')) {
                $table->dropForeign(['descuento_id']); 
                $table->dropColumn('descuento_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->foreignId('descuento_id')->nullable()->after('email')->constrained()->onDelete('set null');
        });

        Schema::dropIfExists('alumno_descuento_maestria');
    }
};
