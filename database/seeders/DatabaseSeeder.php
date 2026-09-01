<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::create() inserta una fila en la tabla "users"
        // Creamos el admin manualmente porque el admin no puede registrarse solo
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@educar.com',
            // Hash::make encripta la contraseña antes de guardarla
            // NUNCA se guarda la contraseña en texto plano
            'password' => Hash::make('admin1234'),
            'role'     => 'admin',
        ]);

        // Profesor de prueba (en el sistema real, el admin lo crea)
        User::create([
            'name'     => 'Prof. Garcia',
            'email'    => 'profesor@educar.com',
            'password' => Hash::make('profesor1234'),
            'role'     => 'profesor',
        ]);
    }
}
