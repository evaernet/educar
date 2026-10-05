<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuarios.index', [
            'usuarios' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,padre',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        User::create($data);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function toggleEstado(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['usuario' => 'No podés desactivar tu propia cuenta.']);
        }

        $user->update(['activo' => ! $user->activo]);

        return back()->with('success', 'Estado de usuario actualizado.');
    }
}
