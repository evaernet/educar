<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Curso;
use App\Models\Deporte;
use App\Models\InscripcionAcademica;
use App\Models\HorarioClase;

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
        $hijos = auth()->user()->hijos()->with([
            'inscripciones' => fn ($query) => $query->where('activo', true)->with('curso'),
            'inscripcionesDeportivas' => fn ($query) => $query->where('activo', true)->with('deporte'),
            'inscripcionesComedor' => fn ($query) => $query->where('activo', true)->with('turno'),
            'inscripcionesTransporte' => fn ($query) => $query->where('activo', true)->with('recorrido'),
        ])->get();

        $cursoIds = $hijos->flatMap(fn ($hijo) => $hijo->inscripciones->pluck('curso_id'))->unique();
        $horariosPorCurso = HorarioClase::whereHas('asignacion', fn ($query) => $query->whereIn('curso_id', $cursoIds))
            ->with('asignacion.materia')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy(fn ($horario) => $horario->asignacion->curso_id);

        return view('dashboard.padre', compact('hijos', 'horariosPorCurso'));
    }
}
