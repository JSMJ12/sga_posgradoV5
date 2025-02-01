<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('dni');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->string('archivo_comprobante')->nullable();
            $table->enum('modalidad_pago', ['unico', 'trimestral', 'otro']);
            $table->boolean('verificado')->default(false);
            $table->timestamps();

            $table->foreign('dni')->references('dni')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}

