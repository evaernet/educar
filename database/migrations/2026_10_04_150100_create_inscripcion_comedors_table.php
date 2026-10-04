<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('inscripcion_comedors', function (Blueprint $table) { $table->id(); $table->foreignId('alumno_id')->constrained('alumnos')->restrictOnDelete(); $table->foreignId('turno_comedor_id')->constrained('turno_comedors')->restrictOnDelete(); $table->boolean('activo')->default(true); $table->timestamps(); $table->unique('alumno_id'); }); }
    public function down(): void { Schema::dropIfExists('inscripcion_comedors'); }
};
