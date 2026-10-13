<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ¿Qué hace?: Guarda los comprobantes de transferencia que suben los padres.
 * Una factura puede tener varios comprobantes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('ruta');
            $table->string('nombre_original');
            $table->string('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('comprobantes'); }
};
