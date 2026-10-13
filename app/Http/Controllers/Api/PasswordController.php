<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * ¿Qué hace?: Permite a cualquier usuario logueado cambiar su contraseña desde la app.
 */
class PasswordController extends Controller
{
    public function cambiar(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'password_actual' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if (! Hash::check($datos['password_actual'], $request->user()->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 422);
        }

        $request->user()->update(['password' => Hash::make($datos['password'])]);
        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }
}
