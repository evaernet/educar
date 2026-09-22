@extends('layouts.app')
@section('titulo', 'Panel Alumno')

{{--
    ¿Qué hace esta vista?: Muestra el panel del alumno logueado.
    Conexión: recibe la variable $alumno desde DashboardController::alumno(),
    que puede venir con datos (si el usuario tiene un alumno vinculado) o
    en null (si por algún motivo no lo tiene, ej: cuenta admin de prueba).
--}}
@section('contenido')
<div class="max-w-2xl mx-auto mt-12">
    <div class="bg-white shadow-md rounded-lg p-10 text-center">
        <div class="text-7xl mb-4">🎒</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-1">
            Hola, {{ auth()->user()->name }}
        </h1>
        <p class="text-gray-500 mb-6">Panel del alumno</p>

        {{-- @if ($alumno) evita el error de "intentar leer una propiedad de null"
             en caso de que este usuario no tenga un alumno vinculado --}}
        @if ($alumno)
            <div class="grid grid-cols-2 gap-4 text-left mt-6">
                <div class="bg-blue-50 rounded p-4">
                    <p class="text-sm text-gray-500">Legajo</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $alumno->legajo }}</p>
                </div>

                <div class="bg-blue-50 rounded p-4">
                    <p class="text-sm text-gray-500">Estado</p>
                    {{-- Cambia el color del texto según esté activo o no --}}
                    <p class="text-lg font-semibold {{ $alumno->activo ? 'text-green-700' : 'text-red-700' }}">
                        {{ $alumno->activo ? 'Activo' : 'Inactivo' }}
                    </p>
                </div>

                <div class="bg-blue-50 rounded p-4 col-span-2">
                    <p class="text-sm text-gray-500">Nombre completo</p>
                    <p class="text-lg font-semibold text-gray-800">
                        {{ $alumno->nombre }} {{ $alumno->apellido }}
                    </p>
                </div>
            </div>
        @else
            <p class="text-gray-500">Todavía no tenés un legajo de alumno vinculado.</p>
        @endif
    </div>
</div>
@endsection
