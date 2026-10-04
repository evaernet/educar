<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Deporte;
use App\Models\HorarioDeporte;
use App\Models\InscripcionDeportiva;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InscripcionDeportivaController extends Controller
{
    public function index()
    {
        return view('deportes.inscripciones', ['inscripciones' => InscripcionDeportiva::with(['alumno', 'deporte'])->orderByDesc('activo')->get(), 'alumnos' => Alumno::where('activo', true)->orderBy('apellido')->get(), 'deportes' => Deporte::where('activo', true)->orderBy('nombre')->get()]);
    }
    public function store(Request $request)
    {
        $data = $request->validate(['alumno_id' => ['required', Rule::exists('alumnos', 'id')->where('activo', true)], 'deporte_id' => ['required', Rule::exists('deportes', 'id')->where('activo', true)]]);
        if (InscripcionDeportiva::where($data)->exists()) return back()->withInput()->withErrors(['deporte_id' => 'El alumno ya estuvo inscripto en este deporte.']);
        if (InscripcionDeportiva::where('alumno_id', $data['alumno_id'])->where('activo', true)->count() >= 2) throw ValidationException::withMessages(['alumno_id' => 'Un alumno solo puede tener hasta dos deportes activos.']);
        $this->validateScheduleConflict($data['alumno_id'], $data['deporte_id']);
        InscripcionDeportiva::create($data);
        return redirect()->route('admin.deportes.inscripciones.index')->with('success', 'Inscripción deportiva registrada correctamente.');
    }
    public function destroy(InscripcionDeportiva $inscripcionDeportiva)
    {
        $inscripcionDeportiva->update(['activo' => false]);
        return redirect()->route('admin.deportes.inscripciones.index')->with('success', 'Inscripción deportiva dada de baja correctamente.');
    }
    private function validateScheduleConflict(int $alumnoId, int $deporteId): void
    {
        $otrosDeportes = InscripcionDeportiva::where('alumno_id', $alumnoId)->where('activo', true)->pluck('deporte_id');
        $conflict = HorarioDeporte::where('deporte_id', $deporteId)->whereExists(function ($query) use ($otrosDeportes) {
            $query->selectRaw('1')->from('horario_deportes as otros')->whereIn('otros.deporte_id', $otrosDeportes)->whereColumn('otros.dia_semana', 'horario_deportes.dia_semana')->whereColumn('otros.hora_inicio', '<', 'horario_deportes.hora_fin')->whereColumn('otros.hora_fin', '>', 'horario_deportes.hora_inicio');
        })->exists();
        if ($conflict) throw ValidationException::withMessages(['deporte_id' => 'El horario de este deporte se superpone con otro deporte activo del alumno.']);
    }
}
