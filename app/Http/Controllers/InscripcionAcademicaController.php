<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\CicloLectivo;
use App\Models\Curso;
use App\Models\InscripcionAcademica;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InscripcionAcademicaController extends Controller
{
    public function index()
    {
        $inscripciones = InscripcionAcademica::with(['alumno', 'cicloLectivo', 'curso.nivel'])
            ->orderByDesc('activo')->orderByDesc('ciclo_lectivo_id')->get();
        return view('inscripciones.index', compact('inscripciones'));
    }

    public function create() { return view('inscripciones.create', $this->formData()); }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        if ($this->existsForCycle($data['alumno_id'], $data['ciclo_lectivo_id'])) {
            return back()->withInput()->withErrors(['alumno_id' => 'El alumno ya posee una inscripción para el ciclo lectivo seleccionado.']);
        }
        InscripcionAcademica::create($data);
        return redirect()->route('admin.inscripciones.index')->with('success', 'Inscripción académica registrada correctamente.');
    }

    public function edit(InscripcionAcademica $inscripcione)
    {
        return view('inscripciones.edit', array_merge(['inscripcion' => $inscripcione], $this->formData()));
    }

    public function update(Request $request, InscripcionAcademica $inscripcione)
    {
        $data = $request->validate($this->rules());
        if ($this->existsForCycle($data['alumno_id'], $data['ciclo_lectivo_id'], $inscripcione->id)) {
            return back()->withInput()->withErrors(['alumno_id' => 'El alumno ya posee una inscripción para el ciclo lectivo seleccionado.']);
        }
        $inscripcione->update($data);
        return redirect()->route('admin.inscripciones.index')->with('success', 'Inscripción académica actualizada correctamente.');
    }

    public function destroy(InscripcionAcademica $inscripcione)
    {
        $inscripcione->update(['activo' => false]);
        return redirect()->route('admin.inscripciones.index')->with('success', 'Inscripción académica dada de baja correctamente.');
    }

    private function formData(): array
    {
        return [
            'alumnos' => Alumno::where('activo', true)->orderBy('apellido')->orderBy('nombre')->get(),
            'ciclos' => CicloLectivo::where('activo', true)->orderByDesc('anio')->get(),
            'cursos' => Curso::where('activo', true)->with('nivel')->orderBy('nombre')->get(),
        ];
    }

    private function rules(): array
    {
        return [
            'alumno_id' => ['required', Rule::exists('alumnos', 'id')->where('activo', true)],
            'ciclo_lectivo_id' => ['required', Rule::exists('ciclo_lectivos', 'id')->where('activo', true)],
            'curso_id' => ['required', Rule::exists('cursos', 'id')->where('activo', true)],
        ];
    }

    private function existsForCycle(int $alumnoId, int $cicloId, ?int $exceptId = null): bool
    {
        return InscripcionAcademica::where('alumno_id', $alumnoId)
            ->where('ciclo_lectivo_id', $cicloId)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }
}
