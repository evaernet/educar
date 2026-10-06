<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Muestra el formulario de login
    // Simplemente devuelve la vista, no hace nada más
    public function mostrarFormulario()
    {
        return view('auth.login');
    }

    // Se ejecuta cuando el usuario aprieta "Entrar"
    public function procesar(Request $request)
    {
        // PASO 1: Validar que los campos no estén vacíos
        // El campo "email" ahora acepta un email (admin, profesor, padre)
        // o un legajo (alumno), por eso ya no exigimos formato de email
        // Si alguna validación falla, Laravel vuelve al formulario
        // con los mensajes de error automáticamente
        $request->validate([
            'email'    => 'required|string',   // requerido (email o legajo)
            'password' => 'required|min:6',    // requerido y mínimo 6 caracteres
        ]);

        // PASO 2: Ver si escribió un legajo en lugar de un email
        // Un email siempre tiene "@". Si no lo tiene, es un legajo de alumno:
        // buscamos al alumno y usamos el email interno de su cuenta
        // (legajo@alumno.educar), que es el que está guardado en "users"
        $usuario = $request->email;

        if (!str_contains($usuario, '@')) {
            $alumno = Alumno::where('legajo', $usuario)->first();

            if ($alumno && $alumno->user) {
                $usuario = $alumno->user->email;
            }
        }

        // PASO 3: Intentar hacer login
        // Auth::attempt busca en la BD un usuario con ese email y contraseña
        // Devuelve true si encontró el usuario, false si no
        $credenciales = [
            'email'    => $usuario,
            'password' => $request->password,
        ];

        if (Auth::attempt($credenciales) && auth()->user()->activo) {
            // Login exitoso
            // Regeneramos la sesión por seguridad (evita ataques de fijación de sesión)
            $request->session()->regenerate();

            // PASO 4: Redirigir según el rol del usuario
            // auth()->user() devuelve el usuario que acaba de loguearse
            $rol = auth()->user()->role;

            // Según el rol, mandamos a distintas rutas
            if ($rol === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($rol === 'profesor') {
                return redirect()->route('profesor.dashboard');
            } elseif ($rol === 'alumno') {
                return redirect()->route('alumno.dashboard');
            } elseif ($rol === 'padre') {
                return redirect()->route('padre.dashboard');
            }
        }

        Auth::logout();

        // PASO 5: Si llegamos acá, el login falló
        // Volvemos al formulario con un mensaje de error
        // withErrors pone el mensaje en $errors dentro de la vista
        // withInput conserva el email escrito (para no tener que escribirlo de nuevo)
        return back()
            ->withErrors(['email' => 'El email, el legajo o la contraseña son incorrectos.'])
            ->withInput($request->only('email'));
    }

    // Cierra la sesión
    public function logout(Request $request)
    {
        Auth::logout(); // Cierra la sesión

        // Limpiamos los datos de sesión por seguridad
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
