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
        Schema::create('postulantes', function (Blueprint $table) {
            $table->string('dni')->primary();
            $table->string('apellidop');
            $table->string('apellidom');
            $table->string('nombre1');
            $table->string('nombre2');
            $table->string('correo_electronico')->nullable();
            $table->string('celular')->nullable();
            $table->string('titulo_profesional')->nullable();
            $table->string('universidad_titulo')->nullable();
            $table->enum('sexo', ['F', 'M']);
            $table->date('fecha_nacimiento');
            $table->string('nacionalidad')->nullable();
            $table->enum('discapacidad', ['Si', 'No']);
            $table->float('porcentaje_discapacidad')->nullable();
            $table->string('codigo_conadis')->nullable();
            $table->string('provincia')->nullable();
            $table->string('etnia')->nullable();
            $table->string('nacionalidad_indigena')->nullable();
            $table->string('canton')->nullable();
            $table->string('direccion')->nullable();
            $table->string('tipo_colegio')->nullable();
            $table->integer('cantidad_miembros_hogar')->nullable();
            $table->string('ingreso_total_hogar')->nullable();
            $table->string('nivel_formacion_padre')->nullable();
            $table->string('nivel_formacion_madre')->nullable();
            $table->string('origen_recursos_estudios')->nullable();
            $table->string('imagen')->nullable(); 
            $table->string('pdf_cedula')->nullable(); ; 
            $table->string('pdf_papelvotacion')->nullable(); ; 
            $table->string('pdf_titulouniversidad')->nullable(); ; 
            $table->string('pdf_conadis')->nullable(); 
            $table->string('pdf_hojavida')->nullable(); 
            $table->boolean('status')->default(false)->comment('Indica si el postulante ha sido aceptado');
            $table->string('pago_matricula')->nullable()->comment('Almacena el comprobante de pago de matrícula');
            $table->string('tipo_discapacidad')->nullable();
            $table->string('carta_aceptacion')->nullable();
            $table->foreignId('maestria_id')->constrained('maestrias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulantes');
    }
};
