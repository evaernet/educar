@extends('layouts.app')
@section('titulo','Registrar ciclo')
@section('contenido')<div class="max-w-xl mx-auto"><h1 class="text-3xl font-bold my-6">Registrar ciclo lectivo</h1><form method="POST" action="{{ route('admin.ciclos.store') }}" class="bg-white p-6 shadow space-y-4">@csrf @include('ciclos.form')<button class="bg-blue-700 text-white px-4 py-2 rounded">Guardar</button></form></div>@endsection
