@extends('layouts.app')
@section('titulo', 'Editar inscripción académica')
@section('contenido')
<div class="max-w-xl mx-auto"><a href="{{ route('admin.inscripciones.index') }}" class="inline-flex rounded-xl border border-cyan-100 bg-white px-3 py-2 font-semibold text-institucional-azul shadow-sm hover:bg-cyan-50">← Volver a inscripciones</a><h1 class="text-3xl font-extrabold text-institucional-oscuro my-6">Editar inscripción académica</h1><form method="POST" action="{{ route('admin.inscripciones.update', $inscripcion) }}" class="rounded-3xl bg-white p-6 shadow-sm space-y-4">@csrf @method('PUT') @include('inscripciones.form')<button class="rounded-xl bg-gradient-to-r from-institucional-azul to-institucional-turquesa px-5 py-3 font-semibold text-white shadow-lg">Guardar cambios</button></form></div>
@endsection
