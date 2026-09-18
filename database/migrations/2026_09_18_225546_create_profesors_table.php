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
        Schema::create('profesors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('legajo', 20)->unique();
            $table->string('dni', 20)->unique();
            $table->string('nombre',20);
            $table->string('apellido', 40);
            $table->string('especialidad', 100);
            $table->boolean('activo')->default(true);
            $table->string('email', 100);
            $table->string('telefono', 20);

            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesors');
    }
};
