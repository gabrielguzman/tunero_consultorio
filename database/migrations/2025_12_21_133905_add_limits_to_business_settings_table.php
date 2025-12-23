<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->integer('max_turnos_por_dia')->nullable()->default(0); // 0 = sin límite
            $table->boolean('allow_overbooking')->default(false); // Permitir sobreturnos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            //
        });
    }
};
