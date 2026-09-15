@extends('layouts.app')
@section('titulo','Editar curso')
@section('contenido')<div class="max-w-xl mx-auto"><h1 class="text-3xl font-bold my-6">Editar curso</h1><form method="POST" action="{{ route('admin.cursos.update',$curso) }}" class="bg-white p-6 shadow space-y-4">@csrf @method('PUT') @include('cursos.form')<button class="bg-blue-700 text-white px-4 py-2 rounded">Guardar cambios</button></form></div>@endsection
