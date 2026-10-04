<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('turno_comedors', function (Blueprint $table) { $table->id(); $table->string('nombre', 100)->unique(); $table->time('hora_inicio'); $table->time('hora_fin'); $table->unsignedSmallInteger('cupo'); $table->boolean('activo')->default(true); $table->timestamps(); }); }
    public function down(): void { Schema::dropIfExists('turno_comedors'); }
};
