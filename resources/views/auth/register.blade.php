@extends('layouts.app')
@section('titulo', 'Registrarse')

@section('contenido')

<div class="flex justify-center items-center min-h-screen -mt-16">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">

        <h1 class="text-2xl font-bold text-center text-blue-800 mb-6">
            📝 Crear Cuenta
        </h1>

        <form action="{{ route('register.procesar') }}" method="POST">
            @csrf

            {{-- Nombre --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Nombre completo</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                    placeholder="Juan Pérez"
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                    placeholder="tu@email.com"
                >
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Selector de Rol --}}
            {{-- Solo mostramos alumno y padre, el admin y profesor no se registran solos --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Me registro como</label>
                <select
                    name="role"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                >
                    <option value="">-- Seleccioná --</option>
                    {{-- old('role') mantiene la selección si hubo error --}}
                    <option value="alumno" {{ old('role') == 'alumno' ? 'selected' : '' }}>
                        🎒 Alumno
                    </option>
                    <option value="padre" {{ old('role') == 'padre' ? 'selected' : '' }}>
                        👨‍👩‍👧 Padre / Madre / Tutor
                    </option>
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Contraseña</label>
                <input
                    type="password"
                    name="password"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                    placeholder="Mínimo 6 caracteres"
                >
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmar Contraseña --}}
            {{-- Este campo DEBE llamarse "password_confirmation" para que --}}
            {{-- la validación "confirmed" funcione automáticamente --}}
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-1">Repetir contraseña</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                    placeholder="Repetí la contraseña"
                >
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded">
                Crear cuenta
            </button>

        </form>

        <p class="text-center text-gray-500 text-sm mt-4">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                Iniciá sesión
            </a>
        </p>

    </div>
</div>

@endsection