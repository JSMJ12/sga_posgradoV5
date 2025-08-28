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
         Schema::table('titulaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('maestria_id')->nullable()->after('alumno_dni');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titulaciones', function (Blueprint $table) {
            //
        });
    }
};
