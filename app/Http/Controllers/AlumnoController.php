<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
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

    public function create()
    {
        return view('alumnos.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'legajo'           => 'required|string|regex:/^[0-9\-]{1,20}$/|max:20|unique:alumnos,legajo',
            'dni'              => 'required|string|regex:/^\d{7,8}$/|max:20|unique:alumnos,dni',
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'domicilio'        => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:255',
        ], [
            'required'        => 'El campo :attribute es obligatorio.',
            'unique'          => 'El :attribute ya está registrado.',
            'max'             => 'El campo :attribute no puede superar :max caracteres.',
            'email'           => 'Ingresá un correo electrónico válido.',
            'date'            => 'Ingresá una fecha válida.',
            'before_or_equal' => 'La fecha no puede ser posterior a hoy.',
        ]);

        // Creamos el usuario para que el alumno pueda loguearse
        // Usamos el legajo como email interno y el DNI como contraseña
        $usuario = User::create([
            'name'     => $datos['nombre'] . ' ' . $datos['apellido'],
            'email'    => $datos['legajo'] . '@alumno.educar',
            'password' => Hash::make($datos['dni']),
            'role'     => 'alumno',
        ]);

        // Creamos el alumno vinculado al usuario
        $datos['user_id'] = $usuario->id;
        Alumno::create($datos);

        return redirect()
            ->route('admin.alumnos.index')
            ->with('success', 'Alumno registrado. Credenciales → Usuario: ' . $datos['legajo'] . ' / Contraseña: ' . $datos['dni']);
    }

    public function show(string $id)
    {
        //
    }

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
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'domicilio'        => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:255',
        ], [
            'required'        => 'El campo :attribute es obligatorio.',
            'unique'          => 'El :attribute ya está registrado.',
            'max'             => 'El campo :attribute no puede superar :max caracteres.',
            'email'           => 'Ingresá un correo electrónico válido.',
            'date'            => 'Ingresá una fecha válida.',
            'before_or_equal' => 'La fecha no puede ser posterior a hoy.',
        ]);

        $alumno->update($datos);

        return redirect()
            ->route('admin.alumnos.index')
            ->with('success', 'Alumno actualizado correctamente.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->update(['activo' => false]);

        return redirect()
            ->route('admin.alumnos.index')
            ->with('success', 'Alumno dado de baja correctamente.');
    }
}