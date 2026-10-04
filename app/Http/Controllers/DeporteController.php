<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeporteController extends Controller
{
    public function index() { return view('deportes.index', ['deportes' => Deporte::with('horarios')->orderBy('nombre')->get()]); }
    public function create() { return view('deportes.create'); }
    public function store(Request $request)
    {
        Deporte::create($request->validate(['nombre' => 'required|string|max:100|unique:deportes,nombre']));
        return redirect()->route('admin.deportes.index')->with('success', 'Deporte registrado correctamente.');
    }
    public function edit(Deporte $deporte) { return view('deportes.edit', compact('deporte')); }
    public function update(Request $request, Deporte $deporte)
    {
        $deporte->update($request->validate(['nombre' => ['required', 'string', 'max:100', Rule::unique('deportes', 'nombre')->ignore($deporte)]]));
        return redirect()->route('admin.deportes.index')->with('success', 'Deporte actualizado correctamente.');
    }
    public function destroy(Deporte $deporte)
    {
        $deporte->update(['activo' => false]);
        return redirect()->route('admin.deportes.index')->with('success', 'Deporte dado de baja correctamente.');
    }
}
