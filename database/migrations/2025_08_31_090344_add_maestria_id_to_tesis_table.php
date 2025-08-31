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
        Schema::table('tesis', function (Blueprint $table) {
            $table->unsignedBigInteger('maestria_id')->after('estado');

            // Si quieres agregar la relación con la tabla maestrias
            $table->foreign('maestria_id')->references('id')->on('maestrias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tesis', function (Blueprint $table) {
            //
        });
    }
};
