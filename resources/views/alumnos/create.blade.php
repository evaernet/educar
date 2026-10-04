@extends('layouts.app')

@section('titulo', 'Registrar alumno')

@section('contenido')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.alumnos.index') }}"
       class="inline-flex rounded-xl border border-cyan-100 bg-white px-3 py-2 font-semibold text-institucional-azul shadow-sm hover:bg-cyan-50">
        ← Volver al listado
    </a>

    <h1 class="text-3xl font-extrabold text-institucional-oscuro my-6">
        Registrar alumno
    </h1>

    <form action="{{ route('admin.alumnos.store') }}"
          method="POST"
          class="bg-white shadow-sm rounded-3xl p-6 sm:p-8 space-y-4">
        @csrf

        @php
            $campos = [
                'legajo' => ['Legajo', 'text', 20, true],
                'dni' => ['DNI', 'text', 20, true],
                'nombre' => ['Nombre', 'text', 100, true],
                'apellido' => ['Apellido', 'text', 100, true],
                'fecha_nacimiento' => ['Fecha de nacimiento', 'date', null, true],
                'domicilio' => ['Domicilio', 'text', 255, true],
                'telefono' => ['Teléfono (opcional)', 'text', 30, false],
                'email' => ['Correo electrónico (opcional)', 'email', 255, false],
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
                    value="{{ old($campo) }}"
                    @if ($limite) maxlength="{{ $limite }}" @endif
                    @if ($obligatorio) required @endif
                    @if ($tipo === 'date') max="{{ now()->toDateString() }}" @endif
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                >

                @error($campo)
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        @endforeach

        <button type="submit"
                class="rounded-xl bg-gradient-to-r from-institucional-azul to-institucional-turquesa px-5 py-3 font-semibold text-white shadow-lg">
            Guardar alumno
        </button>
    </form>
</div>
@endsection
