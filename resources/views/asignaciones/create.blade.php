@extends('layouts.app')
@section('titulo','Nueva asignación')
@section('contenido')<div class="max-w-2xl mx-auto"><h1 class="text-3xl font-bold my-6">Nueva asignación académica</h1><form method="POST" action="{{ route('admin.asignaciones.store') }}" class="bg-white p-6 shadow space-y-4">@csrf @include('asignaciones.form')<button class="bg-blue-700 text-white px-4 py-2 rounded">Guardar asignación</button></form></div>@endsection
