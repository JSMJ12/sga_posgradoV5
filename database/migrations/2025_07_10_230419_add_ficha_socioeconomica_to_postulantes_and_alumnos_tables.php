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
    { Schema::table('postulantes', function (Blueprint $table) {
            $table->string('ficha_socioeconomica')->nullable();
        });

        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('ficha_socioeconomica')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
