@extends('layouts.app')
@section('titulo', 'Panel Admin')

@section('contenido')
<div class="mb-8"><p class="text-sm font-semibold uppercase tracking-[.18em] text-institucional-turquesa">Centro educativo</p><h1 class="mt-1 text-3xl font-extrabold text-institucional-oscuro sm:text-4xl">Panel de administración</h1><p class="mt-2 text-slate-500">Gestioná la información del centro educativo.</p></div>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-8">
    @foreach([['👥','Alumnos activos',$alumnosActivos,'text-blue-600'],['📚','Cursos',$cursos,'text-emerald-600'],['▣','Inscripciones activas',$inscripcionesActivas,'text-violet-600'],['🏆','Deportes',$deportes,'text-amber-600']] as [$icono,$etiqueta,$valor,$color])
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="flex items-center gap-4"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-2xl">{{ $icono }}</span><div><p class="text-sm font-medium text-slate-600">{{ $etiqueta }}</p><p class="text-3xl font-extrabold {{ $color }}">{{ $valor }}</p></div></div></div>
    @endforeach
</div>
<section class="rounded-3xl bg-white p-6 shadow-sm"><h2 class="text-xl font-bold text-institucional-oscuro">Accesos rápidos</h2><p class="mt-1 text-sm text-slate-500">Elegí una tarea para continuar.</p><div class="mt-5 flex flex-wrap gap-3">
        <a href="{{ route('admin.alumnos.index') }}"
            class="inline-block bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-4 py-3 rounded-xl shadow-sm hover:-translate-y-0.5 transition">
            Gestionar alumnos
        </a>
        <a href="{{ route('admin.niveles.index') }}" class="inline-block bg-violet-600 text-white px-4 py-3 rounded-xl">Gestionar niveles</a>
        <a href="{{ route('admin.cursos.index') }}" class="inline-block bg-orange-500 text-white px-4 py-3 rounded-xl">Gestionar cursos</a>
        <a href="{{ route('admin.materias.index') }}" class="inline-block bg-pink-600 text-white px-4 py-3 rounded-xl">Gestionar materias</a>
        <a href="{{ route('admin.asignaciones.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-3 rounded-xl">Asignaciones académicas</a>
        <a href="{{ route('admin.ciclos.index') }}" class="inline-block bg-teal-600 text-white px-4 py-3 rounded-xl">Ciclos lectivos</a>
        <a href="{{ route('admin.horarios.index') }}" class="inline-block bg-cyan-600 text-white px-4 py-3 rounded-xl">Horarios</a>
        <a href="{{ route('admin.inscripciones.index') }}" class="inline-block bg-gradient-to-r from-teal-500 to-emerald-500 text-white px-4 py-3 rounded-xl">Inscripciones académicas</a>
        <a href="{{ route('admin.deportes.index') }}" class="inline-block bg-gradient-to-r from-orange-400 to-amber-500 text-white px-4 py-3 rounded-xl">Deportes</a>
        <a href="{{ route('admin.profesores.index') }}" class="inline-block bg-green-600 text-white px-4 py-3 rounded-xl">Gestionar profesores</a>
    </div></section>
@endsection
