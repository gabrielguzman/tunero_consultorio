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
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('Consultorio Dra. López');
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            // Configuración de Agenda
            $table->integer('start_hour')->default(9); // Hora inicio (ej: 9)
            $table->integer('end_hour')->default(17);  // Hora fin (ej: 17)
            $table->boolean('work_weekends')->default(false); // ¿Trabaja findes?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
