@extends('layouts.app')
@section('titulo', 'Registrar inscripción académica')
@section('contenido')
<div class="max-w-xl mx-auto"><a href="{{ route('admin.inscripciones.index') }}" class="text-blue-700">Volver a inscripciones</a><h1 class="text-3xl font-bold my-6">Registrar inscripción académica</h1><form method="POST" action="{{ route('admin.inscripciones.store') }}" class="bg-white p-6 shadow space-y-4">@csrf @include('inscripciones.form')<button class="bg-violet-700 text-white px-4 py-2 rounded">Guardar inscripción</button></form></div>
@endsection
