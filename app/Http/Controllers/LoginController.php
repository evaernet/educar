<?php

namespace App\Http\Controllers;

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
        // Si alguna validación falla, Laravel vuelve al formulario
        // con los mensajes de error automáticamente
        $request->validate([
            'email'    => 'required|email',    // requerido y formato email
            'password' => 'required|min:6',    // requerido y mínimo 6 caracteres
        ]);

        // PASO 2: Intentar hacer login
        // Auth::attempt busca en la BD un usuario con ese email y contraseña
        // Devuelve true si encontró el usuario, false si no
        $credenciales = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credenciales)) {
            // Login exitoso
            // Regeneramos la sesión por seguridad (evita ataques de fijación de sesión)
            $request->session()->regenerate();

            // PASO 3: Redirigir según el rol del usuario
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

        // PASO 4: Si llegamos acá, el login falló
        // Volvemos al formulario con un mensaje de error
        // withErrors pone el mensaje en $errors dentro de la vista
        // withInput conserva el email escrito (para no tener que escribirlo de nuevo)
        return back()
            ->withErrors(['email' => 'El email o la contraseña son incorrectos.'])
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