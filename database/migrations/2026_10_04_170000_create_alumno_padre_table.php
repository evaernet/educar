<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('alumno_padre', function (Blueprint $table) { $table->id(); $table->foreignId('padre_id')->constrained('users')->restrictOnDelete(); $table->foreignId('alumno_id')->constrained('alumnos')->restrictOnDelete(); $table->timestamps(); $table->unique(['padre_id','alumno_id']); }); } public function down(): void { Schema::dropIfExists('alumno_padre'); } };
