<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('alumnos')
            ->whereNull('user_id')
            ->orderBy('id')
            ->each(function (object $alumno): void {
                $email = $alumno->legajo . '@alumno.educar';

                $usuario = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $alumno->nombre . ' ' . $alumno->apellido,
                        'password' => Hash::make($alumno->dni),
                        'role' => 'alumno',
                        'activo' => true,
                    ]
                );

                DB::table('alumnos')
                    ->where('id', $alumno->id)
                    ->update(['user_id' => $usuario->id]);
            });
    }

    public function down(): void
    {
        // Las cuentas creadas pueden ya haber sido utilizadas; no se eliminan automáticamente.
    }
};
