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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Padre/Madre
            $table->foreignId('health_insurance_id')->nullable()->constrained()->onDelete('set null');
            $table->string('affiliate_number')->nullable();
            $table->string('name');
            $table->date('birth_date');
            $table->enum('gender', ['M', 'F', 'X'])->nullable();
            $table->text('medical_alerts')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
