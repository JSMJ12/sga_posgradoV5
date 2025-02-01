<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetirosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retiros', function (Blueprint $table) {
            $table->id();
            $table->string('alumno_dni');
            $table->string('documento_path'); // Ruta del documento de retiro
            $table->dateTime('fecha_retiro'); // Fecha del retiro
            $table->timestamps();

            // Relación con la tabla alumnos
            $table->foreign('alumno_dni')->references('dni')->on('alumnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retiros');
    }
}
