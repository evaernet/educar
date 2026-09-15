@extends('layouts.app')
@section('titulo','Registrar materia')
@section('contenido')<div class="max-w-xl mx-auto"><h1 class="text-3xl font-bold my-6">Registrar materia</h1><form method="POST" action="{{ route('admin.materias.store') }}" class="bg-white p-6 shadow">@csrf<input name="nombre" required placeholder="Ej. Matemática" class="border rounded px-3 py-2 w-full">@error('nombre')<p class="text-red-600">{{ $message }}</p>@enderror<button class="bg-blue-700 text-white px-4 py-2 rounded mt-4">Guardar</button></form></div>@endsection
