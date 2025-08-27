<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('maestria_id')->nullable();

            // Si quieres relación directa con maestrías
            $table->foreign('maestria_id')
                  ->references('id')
                  ->on('maestrias')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['maestria_id']);
            $table->dropColumn('maestria_id');
        });
    }
};
