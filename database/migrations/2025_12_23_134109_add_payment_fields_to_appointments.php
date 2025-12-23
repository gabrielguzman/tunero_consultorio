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
    Schema::table('appointments', function (Blueprint $table) {
        // Precio final cobrado (puede ser distinto al precio de lista si hace descuento)
        $table->decimal('paid_amount', 10, 2)->nullable(); 
        
        // Medio de pago: 'efectivo', 'transferencia', 'tarjeta', 'obra_social'
        $table->string('payment_method')->nullable(); 
        
        // Fecha exacta del pago (puede ser distinta a la del turno)
        $table->dateTime('payment_date')->nullable();
    });
}

public function down()
{
    Schema::table('appointments', function (Blueprint $table) {
        $table->dropColumn(['paid_amount', 'payment_method', 'payment_date']);
    });
}
};
