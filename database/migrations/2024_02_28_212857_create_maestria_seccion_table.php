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
        Schema::create('maestria_seccion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maestria_id');
            $table->unsignedBigInteger('seccion_id');
            $table->foreign('maestria_id')->references('id')->on('maestrias')->onDelete('cascade');
            $table->foreign('seccion_id')->references('id')->on('secciones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maestria_seccion');
    }
};
