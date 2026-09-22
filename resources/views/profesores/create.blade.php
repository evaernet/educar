@extends('layouts.app')

@section('titulo', 'Registrar profesor')

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
        Registrar profesor
    </h1>

    <form action="{{ route('admin.profesores.store') }}"
          method="POST"
          class="bg-white shadow rounded-lg p-6 space-y-4">
        @csrf

        {{-- @php ... @endphp permite escribir PHP normal dentro del blade.
             Acá armamos un array con la info de cada campo del formulario:
             [ nombre_del_campo => [Etiqueta, tipo de input, largo máximo, obligatorio] ]
             para no repetir el mismo bloque de HTML 7 veces. --}}
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

        {{-- @foreach recorre ese array y genera un bloque de label+input
             por cada campo definido arriba --}}
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
                    value="{{ old($campo) }}"
                    @if ($limite) maxlength="{{ $limite }}" @endif
                    @if ($obligatorio) required @endif
                    class="w-full border border-gray-300 rounded px-3 py-2"
                >

                {{-- @error muestra el mensaje de validación de ESE campo en
                     particular, si el controlador lo rechazó --}}
                @error($campo)
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        @endforeach

        <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">
            Guardar profesor
        </button>
    </form>
</div>
@endsection