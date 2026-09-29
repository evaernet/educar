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

    public function profesor()
    {
        return view('dashboard.profesor');
    }

    public function alumno()
    {
        return view('dashboard.alumno');
    }

    public function padre()
    {
        return view('dashboard.padre');
    }
}
