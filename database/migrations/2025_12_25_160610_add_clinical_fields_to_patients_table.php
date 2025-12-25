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
    Schema::table('patients', function (Blueprint $table) {
        // Datos Personales Extra
        $table->string('sex')->nullable(); // M, F, X
        $table->string('place_of_birth')->nullable();
        
        // Datos Físicos / Salud
        $table->string('blood_type')->nullable(); // A+, 0-, etc.
        $table->integer('height_cm')->nullable(); // En centímetros
        $table->decimal('current_weight', 5, 2)->nullable(); // En KG (ej: 25.50)
        $table->boolean('vaccination_complete')->default(false); // Calendario al día
        
        // Antecedentes Médicos
        $table->text('allergies')->nullable();
        $table->text('background_diseases')->nullable(); // Enfermedades previas
        $table->text('current_medication')->nullable();
        
        // Perinatales (Nacimiento)
        $table->string('pregnancy_type')->nullable(); // Normal / Complicado
        $table->string('birth_type')->nullable(); // Parto / Cesárea
        $table->string('gestational_age')->nullable(); // Ej: "38 semanas"
        $table->decimal('birth_weight', 4, 3)->nullable(); // Ej: 3.500 kg
        
        // Administrativos
        $table->string('clinical_history_number')->nullable();
        $table->date('discharge_date')->nullable(); // Fecha de alta
        $table->boolean('is_active')->default(true); // Estado Activo/Inactivo
        $table->text('observations')->nullable();
    });
}

public function down()
{
    Schema::table('patients', function (Blueprint $table) {
        $table->dropColumn([
            'sex', 'place_of_birth', 'blood_type', 'height_cm', 'current_weight',
            'vaccination_complete', 'allergies', 'background_diseases', 'current_medication',
            'pregnancy_type', 'birth_type', 'gestational_age', 'birth_weight',
            'clinical_history_number', 'discharge_date', 'is_active', 'observations'
        ]);
    });
}
};
