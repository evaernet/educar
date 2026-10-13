<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ¿Qué hace?: Una factura por alumno y por mes (periodo "2026-11").
 * El estado puede ser: pendiente, parcial o pagada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->restrictOnDelete();
            $table->string('periodo', 7);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->decimal('total', 10, 2)->default(0);
            $table->string('estado')->default('pendiente');
            $table->timestamps();
            $table->unique(['alumno_id', 'periodo']);
        });
    }

    public function down(): void { Schema::dropIfExists('facturas'); }
};
