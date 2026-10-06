{{-- @extends dice "este archivo usa el layout app.blade.php como base" --}}
@extends('layouts.app')

{{-- @section llena el hueco @yield('titulo') del layout --}}
@section('titulo', 'Iniciar Sesión')

{{-- @section llena el hueco @yield('contenido') del layout --}}
@section('contenido')

<div class="mx-auto max-w-md">
    <div class="rounded-[2rem] bg-white p-8 shadow-tarjeta sm:p-10">

        <div class="mb-8 text-center"><div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-institucional-azul to-institucional-turquesa text-3xl shadow-lg">🎓</div><h1 class="text-2xl font-extrabold text-institucional-oscuro sm:text-3xl">Educar Para Transformar</h1><p class="mt-2 font-semibold text-institucional-verde">¡Bienvenido/a!</p></div>

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

            {{-- Campo Email o legajo (el alumno entra con su legajo) --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Email o legajo</label>
                <input
                    type="text"
                    name="email"
                    {{-- old('email') recarga el valor que escribiste si hubo error --}}
                    value="{{ old('email') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                    placeholder="Ingresá tu email o legajo"
                >
                {{-- Muestra el error de validación del campo "email" si existe --}}
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Contraseña --}}
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-1">Contraseña</label>
                <input
                    type="password"
                    name="password"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                    placeholder="Ingresá tu contraseña"
                >
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botón de envío --}}
            <button type="submit"
                class="w-full rounded-xl bg-gradient-to-r from-institucional-azul to-institucional-turquesa py-3 font-bold text-white shadow-lg transition hover:-translate-y-0.5">
                Ingresar →
            </button>

        </form>

        {{-- Link al registro --}}
        <p class="text-center text-slate-500 text-sm mt-6">
            ¿No tenés cuenta?
            <a href="{{ route('register') }}" class="font-semibold text-institucional-azul hover:underline">
                Registrate
            </a>
        </p>

    </div>
</div>

@endsection
