@extends('layouts.app')
@section('titulo','Registrar profesor')
@section('contenido')<div class="max-w-2xl mx-auto"><a href="{{ route('admin.profesores.index') }}" class="text-blue-700">Volver al listado</a><h1 class="text-3xl font-bold my-6">Registrar profesor</h1><form method="POST" action="{{ route('admin.profesores.store') }}" class="bg-white shadow rounded p-6 space-y-4">@csrf @include('profesores.form')<button class="bg-blue-700 text-white px-4 py-2 rounded">Guardar profesor</button></form></div>@endsection
