@extends('layouts.app')
@section('titulo', 'Panel Alumno')

@section('contenido')
<div class="flex justify-center mt-20">
    <div class="bg-white shadow-md rounded-lg p-16 text-center">
        <div class="text-8xl mb-6">🎒</div>
        <h1 class="text-4xl font-bold text-gray-800 mb-3">Alumno</h1>
        <p class="text-gray-500">Bienvenido, {{ auth()->user()->name }}</p>
    </div>
</div>
@endsection