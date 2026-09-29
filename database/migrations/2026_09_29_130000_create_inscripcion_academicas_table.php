<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion_academicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->restrictOnDelete();
            $table->foreignId('ciclo_lectivo_id')->constrained('ciclo_lectivos')->restrictOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->restrictOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['alumno_id', 'ciclo_lectivo_id'], 'inscripcion_alumno_ciclo_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_academicas');
    }
};
