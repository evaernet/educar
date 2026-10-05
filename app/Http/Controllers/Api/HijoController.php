<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\HorarioClase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HijoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'padre') {
            return response()->json(['message' => 'Este recurso está disponible solo para padres o tutores.'], 403);
        }

        $hijos = $request->user()->hijos()
            ->where('alumnos.activo', true)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get(['alumnos.id', 'legajo', 'nombre', 'apellido']);

        return response()->json(['hijos' => $hijos]);
    }

    public function show(Request $request, Alumno $alumno): JsonResponse
    {
        if ($request->user()->role !== 'padre') {
            return response()->json(['message' => 'Este recurso está disponible solo para padres o tutores.'], 403);
        }

        $hijo = $request->user()->hijos()
            ->whereKey($alumno->id)
            ->where('alumnos.activo', true)
            ->first();

        if (! $hijo) {
            return response()->json(['message' => 'No tenés acceso a la información de este alumno.'], 404);
        }

        $inscripcionesAcademicas = $hijo->inscripciones()
            ->where('activo', true)
            ->with(['curso.nivel', 'cicloLectivo'])
            ->get();

        $cursoIds = $inscripcionesAcademicas->pluck('curso_id');
        $horarios = HorarioClase::query()
            ->whereHas('asignacion', fn ($query) => $query
                ->whereIn('curso_id', $cursoIds)
                ->where('activo', true))
            ->with(['asignacion.materia', 'asignacion.profesor', 'asignacion.curso', 'asignacion.cicloLectivo'])
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        $deportes = $hijo->inscripcionesDeportivas()
            ->where('activo', true)
            ->with('deporte.horarios')
            ->get()
            ->map(fn ($inscripcion) => [
                'id' => $inscripcion->deporte->id,
                'nombre' => $inscripcion->deporte->nombre,
                'horarios' => $inscripcion->deporte->horarios
                    ->sortBy(fn ($horario) => sprintf('%02d-%s', $horario->dia_semana, $horario->hora_inicio))
                    ->map(fn ($horario) => $this->horarioDeporte($horario))
                    ->values(),
            ]);

        $comedor = $hijo->inscripcionesComedor()
            ->where('activo', true)
            ->with('turno')
            ->first()?->turno;

        $transporte = $hijo->inscripcionesTransporte()
            ->where('activo', true)
            ->with('recorrido')
            ->first()?->recorrido;

        return response()->json([
            'alumno' => [
                'id' => $hijo->id,
                'legajo' => $hijo->legajo,
                'nombre' => $hijo->nombre,
                'apellido' => $hijo->apellido,
            ],
            'academico' => $inscripcionesAcademicas->map(fn ($inscripcion) => [
                'ciclo_lectivo' => $inscripcion->cicloLectivo->anio,
                'nivel' => $inscripcion->curso->nivel->nombre,
                'curso' => $inscripcion->curso->nombre,
                'division' => $inscripcion->curso->division,
                'turno' => $inscripcion->curso->turno,
            ]),
            'horarios' => $horarios->map(fn ($horario) => [
                'dia' => $this->dia($horario->dia_semana),
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'materia' => $horario->asignacion->materia->nombre,
                'profesor' => $horario->asignacion->profesor->nombre . ' ' . $horario->asignacion->profesor->apellido,
            ]),
            'deportes' => $deportes,
            'comedor' => $comedor ? [
                'turno' => $comedor->nombre,
                'hora_inicio' => $comedor->hora_inicio,
                'hora_fin' => $comedor->hora_fin,
            ] : null,
            'transporte' => $transporte ? [
                'recorrido' => $transporte->nombre,
                'zona' => $transporte->zona,
            ] : null,
        ]);
    }

    private function horarioDeporte(object $horario): array
    {
        return [
            'dia' => $this->dia($horario->dia_semana),
            'hora_inicio' => $horario->hora_inicio,
            'hora_fin' => $horario->hora_fin,
        ];
    }

    private function dia(int $dia): string
    {
        return [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'][$dia] ?? 'Sin definir';
    }
}
