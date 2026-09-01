<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarRol
{
    // Esta función se ejecuta en cada request que use este middleware
    // $request  = información de la petición (URL, datos del formulario, etc.)
    // $next     = función que continúa al controlador si todo está bien
    // ...$roles = los roles permitidos (vienen de la ruta, ej: 'role:admin')
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        // auth()->user() devuelve el usuario logueado
        // Si el rol del usuario NO está en la lista de roles permitidos...
        if (!in_array(auth()->user()->role, $roles)) {

            // ...lo mandamos de vuelta al login con un mensaje de error
            return redirect()->route('login')->with('error', 'No tenés permiso para entrar ahí.');
        }

        // Si sí tiene permiso, seguimos adelante normalmente
        return $next($request);
    }
}