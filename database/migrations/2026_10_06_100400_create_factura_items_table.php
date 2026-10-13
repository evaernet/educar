<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ¿Qué hace?: Los renglones de cada factura (cuota, deporte, comedor, transporte).
 * Cuando se paga un ítem se completa pago_id y pagado_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->string('tipo');                           // cuota | deporte | comedor | transporte
            $table->unsignedBigInteger('referencia_id')->nullable(); // deporte_id o recorrido_transporte_id
            $table->string('descripcion');
            $table->decimal('importe', 10, 2);
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->timestamp('pagado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('factura_items'); }
};
