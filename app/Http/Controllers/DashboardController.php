<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Curso;
use App\Models\Deporte;
use App\Models\InscripcionAcademica;

class DashboardController extends Controller
{
    // Cada función simplemente devuelve su vista correspondiente
    // La lógica de seguridad ya la hizo el middleware, no hace falta repetirla acá

    public function admin()
    {
        // view('dashboard.admin') busca el archivo
        // resources/views/dashboard/admin.blade.php
        return view('dashboard.admin', [
            'alumnosActivos' => Alumno::where('activo', true)->count(),
            'cursos' => Curso::where('activo', true)->count(),
            'inscripcionesActivas' => InscripcionAcademica::where('activo', true)->count(),
            'deportes' => Deporte::where('activo', true)->count(),
        ]);
    }

    /**
     * PANEL DEL PROFESOR: profesor()
     *
     * ¿Qué hace?: Muestra el panel del profesor logueado, con sus propios
     * datos (legajo, especialidad, contacto).
     * Conexión: auth()->user() es el usuario logueado. ->profesor usa la
     * relación hasOne que agregamos en User.php, y trae automáticamente
     * la fila de la tabla "profesores" vinculada a ese usuario.
     */
    public function profesor()
    {
        $profesor = auth()->user()->profesor;
        return view('dashboard.profesor', compact('profesor'));
    }

    /**
     * PANEL DEL ALUMNO: alumno()
     *
     * ¿Qué hace?: Muestra el panel del alumno logueado, con sus propios
     * datos (legajo, estado, nombre completo).
     * Conexión: mismo mecanismo que profesor(), pero usando la relación
     * User::alumno().
     */
    public function alumno()
    {
        $alumno = auth()->user()->alumno;
        return view('dashboard.alumno', compact('alumno'));
    }

    public function padre()
    {
        return view('dashboard.padre');
    }
}
