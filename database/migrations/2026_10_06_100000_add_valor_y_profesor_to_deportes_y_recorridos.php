<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ¿Qué hace?: Le agrega a los deportes y a los recorridos de transporte el importe
 * mensual que cobra el colegio, y a los deportes el profesor a cargo.
 * No toca ninguna columna existente: todo es nuevo y opcional (nullable / default 0).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deportes', function (Blueprint $table) {
            $table->decimal('valor_mensual', 10, 2)->default(0);
            $table->foreignId('profesor_id')->nullable()->constrained('profesores')->nullOnDelete();
        });

        Schema::table('recorridos_transporte', function (Blueprint $table) {
            $table->decimal('valor_mensual', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('deportes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profesor_id');
            $table->dropColumn('valor_mensual');
        });
        Schema::table('recorridos_transporte', function (Blueprint $table) {
            $table->dropColumn('valor_mensual');
        });
    }
};
