<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasaTitulacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasa_titulacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cohorte_id');
            $table->unsignedBigInteger('maestria_id');
            $table->integer('numero_matriculados')->nullable();
            $table->integer('numero_maestrantes_aprobados')->nullable();
            $table->integer('retirados')->nullable();
            $table->integer('graduados')->nullable();
            $table->integer('no_graduados')->nullable();
            $table->integer('examen_complexivo')->nullable();
            $table->enum('estado', ['0', '1'])->default('0');
            $table->timestamps();

            $table->foreign('cohorte_id')->references('id')->on('cohortes')->onDelete('cascade');
            $table->foreign('maestria_id')->references('id')->on('maestrias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasa_titulacion');
    }
}
