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
        Schema::create('tutorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->onDelete('cascade');
            $table->string('tutor_dni');
            $table->foreign('tutor_dni')->references('dni')->on('docentes')->onDelete('cascade');
            $table->datetime('fecha');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'realizada'])->default('pendiente');
            $table->enum('tipo', ['virtual', 'presencial'])->default('presencial'); 
            $table->string('link_reunion')->nullable();
            $table->string('lugar')->nullable(); 
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorias');
    }
};
