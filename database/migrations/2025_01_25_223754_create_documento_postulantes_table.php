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
        Schema::create('documentos_postulantes', function (Blueprint $table) {
            $table->id();
            $table->string('dni_postulante');
            $table->string('tipo_documento')->comment('Tipo de documento, e.g., cedula, papel votación, título universidad, etc.');
            $table->string('ruta_documento')->nullable()->comment('Ruta donde se encuentra almacenado el documento');
            $table->boolean('verificado')->default(false)->comment('Estado de verificación del documento');
            $table->timestamps();

            $table->foreign('dni_postulante')->references('dni')->on('postulantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_postulantes');
    }
};
