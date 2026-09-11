@extends('layouts.app')
@section('titulo', 'Panel Admin')

@section('contenido')
<div class="flex justify-center mt-20">
    <div class="bg-white shadow-md rounded-lg p-16 text-center">
        <div class="text-8xl mb-6">🛡️</div>
        <h1 class="text-4xl font-bold text-gray-800 mb-3">Administrador</h1>
        <p class="text-gray-500">Bienvenido, {{ auth()->user()->name }}</p>
        <a href="{{ route('admin.alumnos.index') }}"
            class="inline-block mt-6 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">
            Gestionar alumnos
        </a>
    </div>

</div>
@endsection