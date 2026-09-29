<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsignacionAcademica;
use App\Models\CicloLectivo;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Profesor;

class AsignacionAcademicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asignaciones = AsignacionAcademica::with(['cicloLectivo','curso','materia','profesor'])->latest()->get();
        return view('asignaciones.index', compact('asignaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('asignaciones.create', $this->formData());
    }

    private function formData(): array
    {
        return [
            'ciclos' => CicloLectivo::where('activo', true)->orderByDesc('anio')->get(),
            'cursos' => Curso::where('activo', true)->with('nivel')->orderBy('nombre')->get(),
            'materias' => Materia::where('activo', true)->orderBy('nombre')->get(),
            'profesores' => Profesor::where('activo', true)->orderBy('apellido')->get(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate($this->rules());
        $existe = AsignacionAcademica::where($datos)->where('activo', true)->exists();
        if ($existe) return back()->withInput()->withErrors(['materia_id'=>'La materia ya está asignada a ese curso en este ciclo.']);
        AsignacionAcademica::create($datos);
        return redirect()->route('admin.asignaciones.index')->with('success','Asignación registrada correctamente.');
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
    public function edit(AsignacionAcademica $asignacion)
    {
        return view('asignaciones.edit', array_merge($this->formData(), compact('asignacion')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AsignacionAcademica $asignacion)
    {
        $datos = $request->validate($this->rules());
        $existe = AsignacionAcademica::where($datos)
            ->where('activo', true)
            ->where('id', '!=', $asignacion->id)
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors(['materia_id' => 'La materia ya está asignada a ese curso en este ciclo.']);
        }

        $asignacion->update($datos);
        return redirect()->route('admin.asignaciones.index')->with('success', 'Asignación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AsignacionAcademica $asignacion)
    {
        $asignacion->update(['activo' => false]);
        return redirect()->route('admin.asignaciones.index')->with('success', 'Asignación dada de baja correctamente.');
    }

    private function rules(): array
    {
        return [
            'ciclo_lectivo_id' => 'required|exists:ciclo_lectivos,id',
            'curso_id' => 'required|exists:cursos,id',
            'materia_id' => 'required|exists:materias,id',
            'profesor_id' => 'required|exists:profesores,id',
        ];
    }
}
