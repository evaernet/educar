<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materia;
use Illuminate\Validation\Rule;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('materias.index', ['materias' => Materia::orderBy('nombre')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('materias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Materia::create($request->validate(['nombre'=>'required|string|max:150|unique:materias,nombre']));
        return redirect()->route('admin.materias.index')->with('success','Materia registrada correctamente.');
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
    public function edit(Materia $materia)
    {
        return view('materias.edit', compact('materia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Materia $materia)
    {
        $materia->update($request->validate(['nombre'=>['required','string','max:150',Rule::unique('materias','nombre')->ignore($materia)]]));
        return redirect()->route('admin.materias.index')->with('success','Materia actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materia $materia)
    {
        $materia->update(['activo'=>false]);
        return redirect()->route('admin.materias.index')->with('success','Materia dada de baja correctamente.');
    }
}
