<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ¿Qué hace?: Guarda los importes "generales" que fija el administrador:
 * el valor de la cuota mensual ('cuota') y el del comedor ('comedor').
 * Los deportes y el transporte guardan su valor en su propia tabla.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valores_cuota', function (Blueprint $table) {
            $table->id();
            $table->string('concepto')->unique(); // 'cuota' o 'comedor'
            $table->decimal('importe', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('valores_cuota'); }
};
