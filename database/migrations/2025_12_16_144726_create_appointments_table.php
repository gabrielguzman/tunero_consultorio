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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Usuario (Padre)
            $table->foreignId('patient_id')->constrained(); // Paciente (Hijo)
            $table->foreignId('appointment_type_id')->constrained();

            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('scheduled');

            $table->text('patient_notes')->nullable();
            $table->text('doctor_notes')->nullable();

            $table->boolean('is_overtime')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('cancelled_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
