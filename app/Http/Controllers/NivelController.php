<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nivel;
use Illuminate\Validation\Rule;

class NivelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('niveles.index', ['niveles' => Nivel::orderBy('nombre')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('niveles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Nivel::create($request->validate(['nombre' => 'required|string|max:100|unique:niveles,nombre']));
        return redirect()->route('admin.niveles.index')->with('success', 'Nivel registrado correctamente.');
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
    public function edit(Nivel $nivel)
    {
        return view('niveles.edit', compact('nivel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nivel $nivel)
    {
        $nivel->update($request->validate(['nombre' => ['required','string','max:100',Rule::unique('niveles','nombre')->ignore($nivel)]]));
        return redirect()->route('admin.niveles.index')->with('success', 'Nivel actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nivel $nivel)
    {
        $nivel->update(['activo' => false]);
        return redirect()->route('admin.niveles.index')->with('success', 'Nivel dado de baja correctamente.');
    }
}
