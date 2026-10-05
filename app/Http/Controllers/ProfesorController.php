<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfesorController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));
        $profesores = Profesor::query()
            ->when($buscar !== '', fn ($query) => $query->where(fn ($consulta) => $consulta
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('apellido', 'like', "%{$buscar}%")
                ->orWhere('legajo', 'like', "%{$buscar}%")))
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('profesores.index', compact('profesores', 'buscar'));
    }

    public function create()
    {
        return view('profesores.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->rules());

        DB::transaction(function () use ($datos): void {
            $usuario = User::create([
                'name' => $datos['nombre'] . ' ' . $datos['apellido'],
                'email' => $this->emailDeAcceso($datos['legajo']),
                'password' => Hash::make($datos['dni']),
                'role' => 'profesor',
            ]);

            Profesor::create($datos + ['user_id' => $usuario->id]);
        });

        return redirect()
            ->route('admin.profesores.index')
            ->with('success', 'Profesor registrado. Credenciales → Usuario: ' . $this->emailDeAcceso($datos['legajo']) . ' / Contraseña: ' . $datos['dni']);
    }

    public function show(Profesor $profesor)
    {
        return view('profesores.show', compact('profesor'));
    }

    public function edit(Profesor $profesor)
    {
        return view('profesores.edit', compact('profesor'));
    }

    public function update(Request $request, Profesor $profesor)
    {
        $datos = $request->validate($this->rules($profesor));

        DB::transaction(function () use ($datos, $profesor): void {
            $profesor->update($datos);

            if ($profesor->user) {
                $profesor->user->update([
                    'name' => $datos['nombre'] . ' ' . $datos['apellido'],
                    'email' => $this->emailDeAcceso($datos['legajo']),
                ]);
            }
        });

        return redirect()->route('admin.profesores.index')->with('success', 'Profesor actualizado correctamente.');
    }

    public function destroy(Profesor $profesor)
    {
        DB::transaction(function () use ($profesor): void {
            $profesor->update(['activo' => false]);
            $profesor->user?->update(['activo' => false]);
        });

        return redirect()->route('admin.profesores.index')->with('success', 'Profesor dado de baja correctamente.');
    }

    private function emailDeAcceso(string $legajo): string
    {
        return $legajo . '@profesor.educar';
    }

    private function rules(?Profesor $profesor = null): array
    {
        return [
            'legajo' => ['required', 'string', 'max:20', Rule::unique('profesores', 'legajo')->ignore($profesor)],
            'dni' => ['required', 'string', 'regex:/^\d{7,8}$/', Rule::unique('profesores', 'dni')->ignore($profesor)],
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'especialidad' => 'required|string|max:150',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:30',
        ];
    }
}
