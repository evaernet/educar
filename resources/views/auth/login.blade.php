{{-- @extends dice "este archivo usa el layout app.blade.php como base" --}}
@extends('layouts.app')

{{-- @section llena el hueco @yield('titulo') del layout --}}
@section('titulo', 'Iniciar Sesión')

{{-- @section llena el hueco @yield('contenido') del layout --}}
@section('contenido')

<div class="flex justify-center items-center min-h-screen -mt-16">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">

        <h1 class="text-2xl font-bold text-center text-blue-800 mb-6">
            🏫 Iniciar Sesión
        </h1>

        {{-- Mostrar mensajes de error generales (ej: "No tenés permiso") --}}
        @if (session('error'))
            <div class="bg-red-100 text-red-700 border border-red-300 rounded p-3 mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- action apunta a la ruta que procesa el login --}}
        {{-- method="POST" porque estamos enviando datos --}}
        <form action="{{ route('login.procesar') }}" method="POST">

            {{-- @csrf es obligatorio en todos los formularios POST de Laravel --}}
            {{-- Protege contra ataques CSRF (peticiones falsas desde otros sitios) --}}
            @csrf

            {{-- Cambiar el campo email por legajo --}}
{{-- Cambiar el campo email por legajo --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-1">Usuario</label>
    <input
        type="text"
        name="legajo"
        value="{{ old('legajo') }}"
        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
        placeholder="Tu usuario"
    >
    @error('legajo')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6">
    <label class="block text-gray-700 font-medium mb-1">Contraseña (DNI)</label>
    <input
        type="password"
        name="password"
        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
        placeholder="Tu DNI"
    >
    @error('password')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

            {{-- Botón de envío --}}
            <button type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 rounded">
                Entrar
            </button>

        </form>

        {{-- Link al registro --}}
        <p class="text-center text-gray-500 text-sm mt-4">
            ¿No tenés cuenta?
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">
                Registrate acá
            </a>
        </p>

    </div>
</div>

@endsection