@extends('layouts.app')

@section('titulo', 'Editar profesor')

@section('contenido')
<div class="max-w-2xl mx-auto">
    {{-- Volver al listado (ida y vuelta rápida a esta misma sección) --}}
    <a href="{{ route('admin.profesores.index') }}"
       class="text-blue-700 hover:underline">
        Volver al listado
    </a>

    {{-- Volver directo al panel principal, sin pasar por el listado --}}
    <a href="{{ route('admin.dashboard') }}"
       class="text-blue-700 hover:underline ml-4">
        Volver al panel
    </a>

    <h1 class="text-3xl font-bold text-gray-800 my-6">
        Editar profesor
    </h1>

    {{-- Misma idea que create.blade.php, con dos diferencias:
         1) action apunta a 'update' en vez de 'store'
         2) @method('PUT') porque estamos editando, no creando
         3) value="{{ old($campo, $profesor->{$campo}) }}" -> si el usuario
            ya escribió algo y hubo un error de validación, muestra lo que
            escribió (old); si es la primera vez que entra, muestra el
            dato actual del profesor ($profesor->{$campo}) --}}
    <form action="{{ route('admin.profesores.update', $profesor) }}"
        method="POST"
        class="bg-white shadow rounded-lg p-6 space-y-4">
      @csrf
      @method('PUT')

        @php
            $campos = [
                'legajo' => ['Legajo', 'text', 20, true],
                'dni' => ['DNI', 'text', 20, true],
                'nombre' => ['Nombre', 'text', 100, true],
                'apellido' => ['Apellido', 'text', 100, true],
                'especialidad' => ['Especialidad', 'text', 100, true],
                'email' => ['Correo electrónico', 'email', 255, true],
                'telefono' => ['Teléfono', 'text', 30, true],
            ];
        @endphp

        @foreach ($campos as $campo => [$etiqueta, $tipo, $limite, $obligatorio])
            <div>
                <label for="{{ $campo }}"
                       class="block font-medium text-gray-700 mb-1">
                    {{ $etiqueta }}
                </label>

                <input
                    id="{{ $campo }}"
                    name="{{ $campo }}"
                    type="{{ $tipo }}"
                    value="{{ old($campo, $profesor->{$campo}) }}"
                    @if ($limite) maxlength="{{ $limite }}" @endif
                    @if ($obligatorio) required @endif
                    class="w-full border border-gray-300 rounded px-3 py-2"
                >

                @error($campo)
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        @endforeach

        <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">
            Guardar cambios
        </button>
    </form>
</div>
@endsection