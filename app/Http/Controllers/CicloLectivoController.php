<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CicloLectivo;
use Illuminate\Validation\Rule;

class CicloLectivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('ciclos.index', ['ciclos' => CicloLectivo::orderByDesc('anio')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ciclos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        CicloLectivo::create($request->validate(['anio'=>'required|integer|min:2000|max:2100|unique:ciclo_lectivos,anio','fecha_inicio'=>'required|date','fecha_fin'=>'required|date|after_or_equal:fecha_inicio']));
        return redirect()->route('admin.ciclos.index')->with('success','Ciclo registrado correctamente.');
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
    public function edit(CicloLectivo $ciclo)
    {
        return view('ciclos.edit', compact('ciclo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CicloLectivo $ciclo)
    {
        $ciclo->update($request->validate(['anio'=>['required','integer','min:2000','max:2100',Rule::unique('ciclo_lectivos','anio')->ignore($ciclo)],'fecha_inicio'=>'required|date','fecha_fin'=>'required|date|after_or_equal:fecha_inicio']));
        return redirect()->route('admin.ciclos.index')->with('success','Ciclo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CicloLectivo $ciclo)
    {
        $ciclo->update(['activo'=>false]);
        return redirect()->route('admin.ciclos.index')->with('success','Ciclo dado de baja correctamente.');
    }
}
