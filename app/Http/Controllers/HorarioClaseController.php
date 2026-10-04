<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioClase;
use App\Models\AsignacionAcademica;

class HorarioClaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $horarios = HorarioClase::with('asignacion.curso','asignacion.materia','asignacion.profesor')->orderBy('dia_semana')->orderBy('hora_inicio')->get();
        return view('horarios.index', compact('horarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('horarios.create', ['asignaciones' => $this->asignacionesActivas()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate($this->rules());
        $this->validarConflicto($datos);
        HorarioClase::create($datos);
        return redirect()->route('admin.horarios.index')->with('success','Horario registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HorarioClase $horario)
    {
        return view('horarios.edit', ['horario'=>$horario, 'asignaciones'=>$this->asignacionesActivas()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HorarioClase $horario)
    {
        $datos = $request->validate($this->rules());
        $this->validarConflicto($datos, $horario);
        $horario->update($datos);
        return redirect()->route('admin.horarios.index')->with('success','Horario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HorarioClase $horario)
    {
        $horario->delete();
        return redirect()->route('admin.horarios.index')->with('success','Horario eliminado correctamente.');
    }

    private function asignacionesActivas()
    {
        return AsignacionAcademica::where('activo', true)->with(['curso', 'materia', 'profesor'])->get();
    }

    private function rules(): array
    {
        return ['asignacion_academica_id'=>'required|exists:asignacion_academicas,id','dia_semana'=>'required|integer|between:1,6','hora_inicio'=>'required|date_format:H:i','hora_fin'=>'required|date_format:H:i|after:hora_inicio'];
    }

    private function validarConflicto(array $datos, ?HorarioClase $horario = null): void
    {
        $asignacion = AsignacionAcademica::findOrFail($datos['asignacion_academica_id']);
        $conflicto = HorarioClase::query()
            ->where('dia_semana', $datos['dia_semana'])
            ->where('hora_inicio', '<', $datos['hora_fin'])
            ->where('hora_fin', '>', $datos['hora_inicio'])
            ->when($horario, fn ($query) => $query->where('id', '!=', $horario->id))
            ->whereHas('asignacion', function ($query) use ($asignacion) {
                $query->where('activo', true)
                    ->where(function ($relacion) use ($asignacion) {
                        $relacion->where('profesor_id', $asignacion->profesor_id)
                            ->orWhere('curso_id', $asignacion->curso_id);
                    });
            })
            ->exists();

        if ($conflicto) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'hora_inicio' => 'Existe un conflicto de horario para el profesor o el curso seleccionado.',
            ]);
        }
    }
}
