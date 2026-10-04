@extends('layouts.app')
@section('titulo', 'Registrar deporte')
@section('contenido')
<div class="max-w-xl mx-auto"><h1 class="text-3xl font-bold my-6">Registrar deporte</h1><form method="POST" action="{{ route('admin.deportes.store') }}" class="bg-white p-6 shadow">@csrf <label class="block font-medium">Nombre</label><input name="nombre" required value="{{ old('nombre') }}" class="border rounded px-3 py-2 w-full">@error('nombre')<p class="text-red-600">{{ $message }}</p>@enderror<button class="bg-emerald-700 text-white px-4 py-2 rounded mt-4">Guardar</button></form></div>
@endsection
