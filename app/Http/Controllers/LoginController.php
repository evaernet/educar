<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;

class LoginController extends Controller
{
    public function mostrarFormulario()
    {
        return view('auth.login');
    }

    public function procesar(Request $request)
    {
        $esEmail = str_contains($request->input('legajo', ''), '@');

        if ($esEmail) {

            $request->validate([
                'legajo'   => 'required|email',
                'password' => 'required|string',
            ]);

            if (Auth::attempt([
                'email'    => $request->legajo,
                'password' => $request->password,
            ])) {
                $request->session()->regenerate();

                /** @var \App\Models\User $usuario */
                $usuario = Auth::user();

                if ($usuario->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($usuario->role === 'profesor') {
                    return redirect()->route('profesor.dashboard');
                }
                elseif ($usuario->role === 'padre') {
    return redirect()->route('padre.dashboard');
}
            }

            return back()
                ->withErrors(['legajo' => 'Usuario o contraseña incorrectos.'])
                ->withInput($request->only('legajo'));

        } else {

            $request->validate([
                'legajo'   => 'required|string',
                'password' => 'required|string',
            ]);

            $alumno = Alumno::where('legajo', $request->legajo)->first();

            if (!$alumno || !$alumno->user) {
                return back()
                    ->withErrors(['legajo' => 'Usuario o contraseña incorrectos.'])
                    ->withInput($request->only('legajo'));
            }

            if (!$alumno->activo) {
    return back()->withErrors(['legajo' => 'El alumno se encuentra dado de baja.']);
}

            if (Auth::attempt([
                'email'    => $alumno->user->email,
                'password' => $request->password,
            ])) {
                $request->session()->regenerate();
                return redirect()->route('alumno.dashboard');
            }

            return back()
                ->withErrors(['legajo' => 'Usuario o contraseña incorrectos.'])
                ->withInput($request->only('legajo'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}