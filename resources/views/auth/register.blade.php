@extends('layouts.app')
@section('titulo', 'Registrarse')

@section('contenido')

<div class="mx-auto max-w-md">
    <div class="rounded-[2rem] bg-white p-8 shadow-tarjeta sm:p-10">

        <div class="mb-7 text-center"><div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-institucional-azul to-institucional-turquesa text-2xl">🎓</div><h1 class="text-2xl font-extrabold text-institucional-oscuro">Crear cuenta</h1><p class="mt-1 text-sm text-institucional-verde">Sumate a nuestra comunidad educativa</p></div>

        <form action="{{ route('register.procesar') }}" method="POST">
            @csrf

            {{-- Nombre --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Nombre completo</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
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
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
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
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
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
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                    placeholder="Mínimo 8 caracteres, mayúscula y número"
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
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                    placeholder="Repetí la contraseña"
                >
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-gradient-to-r from-institucional-azul to-institucional-turquesa py-3 font-bold text-white shadow-lg transition hover:-translate-y-0.5">
                Crear cuenta
            </button>

        </form>

        <p class="text-center text-slate-500 text-sm mt-6">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="font-semibold text-institucional-azul hover:underline">
                Iniciá sesión
            </a>
        </p>

    </div>
</div>

@endsection
