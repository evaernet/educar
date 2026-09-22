@extends('layouts.app')
@section('titulo', 'Panel Profesor')

{{--
    ¿Qué hace esta vista?: Muestra el panel del profesor logueado.
    Conexión: recibe la variable $profesor desde DashboardController::profesor(),
    igual que la vista de alumno, usando la relación User::profesor().
--}}
@section('contenido')
<div class="max-w-2xl mx-auto mt-12">
    <div class="bg-white shadow-md rounded-lg p-10 text-center">
        <div class="text-7xl mb-4">📚</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-1">
            Hola, {{ auth()->user()->name }}
        </h1>
        <p class="text-gray-500 mb-6">Panel del profesor</p>

        @if ($profesor)
            <div class="grid grid-cols-2 gap-4 text-left mt-6">
                <div class="bg-blue-50 rounded p-4">
                    <p class="text-sm text-gray-500">Legajo</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $profesor->legajo }}</p>
                </div>

                <div class="bg-blue-50 rounded p-4">
                    <p class="text-sm text-gray-500">Especialidad</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $profesor->especialidad }}</p>
                </div>

                <div class="bg-blue-50 rounded p-4 col-span-2">
                    <p class="text-sm text-gray-500">Contacto</p>
                    <p class="text-lg font-semibold text-gray-800">
                        {{ $profesor->email }} · {{ $profesor->telefono }}
                    </p>
                </div>
            </div>
        @else
            <p class="text-gray-500">Todavía no tenés un legajo de profesor vinculado.</p>
        @endif
    </div>
</div>
@endsection
