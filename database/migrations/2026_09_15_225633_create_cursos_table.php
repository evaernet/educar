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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nivel_id')->constrained('niveles')->restrictOnDelete();
            $table->string('nombre', 100);
            $table->string('division', 10)->nullable();
            $table->string('turno', 30)->nullable();
            $table->boolean('activo')->default(true);
            $table->unique(['nivel_id', 'nombre', 'division']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
