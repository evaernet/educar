<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Muestra el formulario de registro
    public function mostrarFormulario()
    {
        return view('auth.register');
    }

    // Se ejecuta cuando el usuario aprieta "Registrarme"
    public function procesar(Request $request)
    {
        // PASO 1: Validar los datos del formulario
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email', // unique verifica que no exista en la tabla users
            'password'              => 'required|min:6|confirmed', // "confirmed" busca un campo "password_confirmation"
            'role'                  => 'required|in:alumno,padre', // solo puede ser alumno o padre
        ], [
            // Acá personalizamos los mensajes de error en español
            'name.required'         => 'El nombre es obligatorio.',
            'email.required'        => 'El email es obligatorio.',
            'email.unique'          => 'Ya existe una cuenta con ese email.',
            'password.min'          => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'    => 'Las contraseñas no coinciden.',
            'role.required'         => 'Seleccioná un rol.',
            'role.in'               => 'Rol no válido.',
        ]);

        // PASO 2: Crear el usuario en la base de datos
        // Hash::make encripta la contraseña ANTES de guardarla
        // NUNCA guardes contraseñas en texto plano
        $usuario = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // PASO 3: Loguear al usuario automáticamente después de registrarse
        Auth::login($usuario);

        // PASO 4: Redirigir al dashboard que le corresponde
        if ($request->role === 'alumno') {
            return redirect()->route('alumno.dashboard');
        } else {
            return redirect()->route('padre.dashboard');
        }
    }
}