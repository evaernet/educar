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
        if (Schema::hasTable('asignacion_academicas')) {
            Schema::table('asignacion_academicas', function (Blueprint $table) {
                $table->unique(['ciclo_lectivo_id','curso_id','materia_id'], 'asig_academica_unica');
            });
            return;
        }
        Schema::create('asignacion_academicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_lectivo_id')->constrained('ciclo_lectivos')->restrictOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->restrictOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->restrictOnDelete();
            $table->foreignId('profesor_id')->constrained('profesores')->restrictOnDelete();
            $table->unique(['ciclo_lectivo_id','curso_id','materia_id'], 'asig_academica_unica');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_academicas');
    }
};
