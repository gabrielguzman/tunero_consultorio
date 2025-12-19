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
        Schema::create('patient_files', function (Blueprint $table) {
            $table->id();
            // Relación con el paciente
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            $table->string('file_path');     // Dónde está guardado en la carpeta storage
            $table->string('original_name'); // Nombre real (ej: "analisis_sangre.pdf")
            $table->string('file_type');     // pdf, jpg, png

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_files');
    }
};
