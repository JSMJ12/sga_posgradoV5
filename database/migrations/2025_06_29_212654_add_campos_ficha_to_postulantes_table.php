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
        Schema::table('postulantes', function (Blueprint $table) {
            // Información personal
            $table->string('telefono_convencional', 20)->nullable();
            $table->tinyInteger('edad')->nullable();
            $table->string('tipo_sangre', 5)->nullable();
            $table->string('anios_residencia', 5)->nullable();
            $table->string('libreta_militar', 50)->nullable();
            $table->string('numero_matricula', 50)->nullable();

            // Residencia
            $table->string('pais_residencia', 100)->nullable();
            $table->string('parroquia', 100)->nullable();
            $table->string('calle_principal', 150)->nullable();
            $table->string('numero_direccion', 20)->nullable();
            $table->string('calle_secundaria', 150)->nullable();
            $table->text('referencia_direccion')->nullable();
            $table->string('telefono_domicilio', 20)->nullable();
            $table->string('celular_residencia', 20)->nullable();

            // Contacto emergencia
            $table->string('contacto_apellidos', 150)->nullable();
            $table->string('contacto_nombres', 150)->nullable();
            $table->string('contacto_parentesco', 50)->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->string('contacto_celular', 20)->nullable();

            // Académica
            $table->string('especialidad_bachillerato', 150)->nullable();
            $table->string('colegio_bachillerato', 150)->nullable();
            $table->string('ciudad_bachillerato', 100)->nullable();
            $table->string('especialidad_mencion', 150)->nullable();
            $table->string('ciudad_universidad', 100)->nullable();
            $table->string('pais_universidad', 100)->nullable();
            $table->string('registro_senescyt', 100)->nullable();
            $table->string('titulo_posgrado', 150)->nullable();
            $table->string('denominacion_posgrado', 150)->nullable();
            $table->string('universidad_posgrado', 150)->nullable();
            $table->string('ciudad_posgrado', 100)->nullable();
            $table->string('pais_posgrado', 100)->nullable();

            // Laboral
            $table->string('lugar_trabajo', 150)->nullable();
            $table->string('funcion_laboral', 150)->nullable();
            $table->string('ciudad_trabajo', 100)->nullable();
            $table->text('direccion_trabajo')->nullable();
            $table->string('telefono_trabajo', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postulantes', function (Blueprint $table) {
            //
        });
    }
};
