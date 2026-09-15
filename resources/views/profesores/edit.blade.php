@extends('layouts.app')
@section('titulo','Editar profesor')
@section('contenido')<div class="max-w-2xl mx-auto"><a href="{{ route('admin.profesores.index') }}" class="text-blue-700">Volver al listado</a><h1 class="text-3xl font-bold my-6">Editar profesor</h1><form method="POST" action="{{ route('admin.profesores.update',$profesor) }}" class="bg-white shadow rounded p-6 space-y-4">@csrf @method('PUT') @include('profesores.form')<button class="bg-blue-700 text-white px-4 py-2 rounded">Guardar cambios</button></form></div>@endsection
