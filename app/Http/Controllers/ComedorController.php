<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\InscripcionComedor;
use App\Models\TurnoComedor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ComedorController extends Controller
{
    public function index()
    {
        return view('comedor.index', [
            'turnos' => TurnoComedor::withCount([
                'inscripciones as inscriptos' => fn ($query) => $query->where('activo', true),
            ])->orderBy('hora_inicio')->get(),
            'inscripciones' => InscripcionComedor::with(['alumno', 'turno'])->where('activo', true)->get(),
            'alumnos' => Alumno::where('activo', true)->orderBy('apellido')->get(),
        ]);
    }

    public function storeTurno(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100|unique:turno_comedors,nombre',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'cupo' => 'required|integer|min:1|max:1000',
        ]);

        TurnoComedor::create($datos);

        return redirect()->route('admin.comedor.index')
            ->with('success', 'Turno de comedor registrado correctamente.');
    }

    public function storeInscripcion(Request $request)
    {
        $datos = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'turno_comedor_id' => 'required|exists:turno_comedors,id',
        ]);

        if (InscripcionComedor::where('alumno_id', $datos['alumno_id'])->exists()) {
            throw ValidationException::withMessages([
                'alumno_id' => 'El alumno ya posee una inscripción de comedor.',
            ]);
        }

        $turno = TurnoComedor::findOrFail($datos['turno_comedor_id']);

        if (! $turno->activo || $turno->inscripciones()->where('activo', true)->count() >= $turno->cupo) {
            throw ValidationException::withMessages([
                'turno_comedor_id' => 'El turno seleccionado no tiene cupos disponibles.',
            ]);
        }

        InscripcionComedor::create($datos);

        return redirect()->route('admin.comedor.index')
            ->with('success', 'Alumno inscripto al comedor correctamente.');
    }

    public function destroyInscripcion(InscripcionComedor $inscripcionComedor)
    {
        $inscripcionComedor->update(['activo' => false]);

        return redirect()->route('admin.comedor.index')
            ->with('success', 'Inscripción de comedor dada de baja.');
    }
}
