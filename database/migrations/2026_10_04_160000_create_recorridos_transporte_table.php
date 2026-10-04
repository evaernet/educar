<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('recorridos_transporte', function (Blueprint $table) { $table->id(); $table->string('nombre', 100)->unique(); $table->string('zona', 150); $table->unsignedSmallInteger('cupo'); $table->boolean('activo')->default(true); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('recorridos_transporte'); } };
