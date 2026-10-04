@extends('layouts.app')
@section('titulo','Editar materia')
@section('contenido')<div class="max-w-xl mx-auto"><h1 class="text-3xl font-bold my-6">Editar materia</h1><form method="POST" action="{{ route('admin.materias.update',$materia) }}" class="bg-white p-6 shadow">@csrf @method('PUT')<input name="nombre" required value="{{ old('nombre',$materia->nombre) }}" class="border rounded px-3 py-2 w-full">@error('nombre')<p class="text-red-600">{{ $message }}</p>@enderror<button class="bg-blue-700 text-white px-4 py-2 rounded mt-4">Guardar cambios</button></form></div>@endsection
