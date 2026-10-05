<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('profesores')
            ->whereNull('user_id')
            ->orderBy('id')
            ->each(function (object $profesor): void {
                $email = $profesor->legajo . '@profesor.educar';

                $usuario = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $profesor->nombre . ' ' . $profesor->apellido,
                        'password' => Hash::make($profesor->dni),
                        'role' => 'profesor',
                        'activo' => true,
                    ]
                );

                DB::table('profesores')
                    ->where('id', $profesor->id)
                    ->update(['user_id' => $usuario->id]);
            });
    }

    public function down(): void
    {
        // Las cuentas pueden tener actividad posterior; no se eliminan automáticamente.
    }
};
