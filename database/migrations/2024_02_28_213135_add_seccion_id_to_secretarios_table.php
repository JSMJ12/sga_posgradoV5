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
        Schema::table('secretarios', function (Blueprint $table) {
            $table->foreignId('seccion_id')->nullable();
            $table->foreign('seccion_id')->references('id')->on('secciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('secretarios', function (Blueprint $table) {
            // Eliminar la restricción de clave externa primero
            $table->dropForeign(['seccion_id']);
            // Luego eliminar la columna
            $table->dropColumn('seccion_id');
        });
    }
};
