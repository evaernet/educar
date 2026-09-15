<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Nivel;
use Illuminate\Validation\Rule;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cursos = Curso::with('nivel')->orderBy('nivel_id')->orderBy('nombre')->get();
        return view('cursos.index', compact('cursos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cursos.create', ['niveles' => Nivel::where('activo', true)->orderBy('nombre')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Curso::create($request->validate($this->rules()));
        return redirect()->route('admin.cursos.index')->with('success', 'Curso registrado correctamente.');
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
    public function edit(Curso $curso)
    {
        return view('cursos.edit', ['curso' => $curso, 'niveles' => Nivel::where('activo', true)->orderBy('nombre')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Curso $curso)
    {
        $curso->update($request->validate($this->rules($curso)));
        return redirect()->route('admin.cursos.index')->with('success', 'Curso actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curso $curso)
    {
        $curso->update(['activo' => false]);
        return redirect()->route('admin.cursos.index')->with('success', 'Curso dado de baja correctamente.');
    }

    private function rules(?Curso $curso = null): array
    {
        return ['nivel_id' => 'required|exists:niveles,id', 'nombre' => 'required|string|max:100', 'division' => 'nullable|string|max:10', 'turno' => 'nullable|string|max:30'];
    }
}
