<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use Illuminate\Validation\Rule;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'buscar' => 'nullable|string|max:100',
        ]);

        $buscar = trim($request->input('buscar') ?? '');

        $alumnos = Alumno::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($consulta) use ($buscar) {
                    $consulta->where('nombre', 'like', '%' . $buscar . '%')
                        ->orWhere('apellido', 'like', '%' . $buscar . '%')
                        ->orWhere('legajo', 'like', '%' . $buscar . '%');
                });
            })
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('alumnos.index', compact('alumnos', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('alumnos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
        {
            $datos = $request->validate([
                'legajo' => 'required|string|max:20|unique:alumnos,legajo',
                'dni' => 'required|string|max:20|unique:alumnos,dni',
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'fecha_nacimiento' => 'required|date|before_or_equal:today',
                'domicilio' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:255',
            ], [
                'required' => 'El campo :attribute es obligatorio.',
                'unique' => 'El :attribute ya está registrado.',
                'max' => 'El campo :attribute no puede superar :max caracteres.',
                'email' => 'Ingresá un correo electrónico válido.',
                'date' => 'Ingresá una fecha válida.',
                'before_or_equal' => 'La fecha no puede ser posterior a hoy.',
            ]);

            Alumno::create($datos);

            return redirect()
                ->route('admin.alumnos.index')
                ->with('success', 'Alumno registrado correctamente.');
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
    public function edit(Alumno $alumno)
    {
        return view('alumnos.edit', compact('alumno'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $datos = $request->validate([
            'legajo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('alumnos', 'legajo')->ignore($alumno),
            ],
            'dni' => [
                'required',
                'string',
                'max:20',
                Rule::unique('alumnos', 'dni')->ignore($alumno),
            ],
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'unique' => 'El :attribute ya está registrado.',
            'max' => 'El campo :attribute no puede superar :max caracteres.',
            'email' => 'Ingresá un correo electrónico válido.',
            'date' => 'Ingresá una fecha válida.',
            'before_or_equal' => 'La fecha no puede ser posterior a hoy.',
        ]);

        $alumno->update($datos);

        return redirect()
            ->route('admin.alumnos.index')
            ->with('success', 'Alumno actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alumno $alumno)
    {
        $alumno->update(['activo' => false]);

        return redirect()
            ->route('admin.alumnos.index')
            ->with('success', 'Alumno dado de baja correctamente.');
    }
}
