<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ¿Qué hace?: Cada pago es lo que un padre abona de una factura (uno o varios ítems).
 * El pago es el "comprobante de pago" que emite el sistema.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('numero')->unique();
            $table->decimal('total', 10, 2);
            $table->date('fecha_pago');
            $table->string('medio')->default('transferencia');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('pagos'); }
};
