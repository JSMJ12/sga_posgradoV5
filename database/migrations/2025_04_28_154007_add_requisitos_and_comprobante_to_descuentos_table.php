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
        Schema::table('descuentos', function (Blueprint $table) {
            $table->json('requisitos')->nullable()->after('activo');
            $table->string('comprobante')->nullable()->after('requisitos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('descuentos', function (Blueprint $table) {
            //
        });
    }
};
