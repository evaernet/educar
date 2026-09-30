@extends('layouts.app')
@section('titulo', 'Panel Profesor')

@section('contenido')
<div class="mb-8"><p class="text-sm font-semibold uppercase tracking-[.18em] text-institucional-turquesa">Espacio docente</p><h1 class="mt-1 text-3xl font-extrabold text-institucional-oscuro sm:text-4xl">Hola, {{ auth()->user()->name }}</h1><p class="mt-2 text-slate-500">Organizá tus cursos, horarios y actividades académicas.</p></div>
<div class="grid gap-5 md:grid-cols-3"><div class="rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-500 p-6 text-white shadow-lg"><span class="text-4xl">📚</span><h2 class="mt-8 text-xl font-bold">Mis cursos</h2><p class="mt-2 text-sm text-white/80">Accedé a tus asignaciones académicas.</p></div><div class="rounded-3xl bg-white p-6 shadow-sm"><span class="text-3xl">◫</span><h2 class="mt-8 text-xl font-bold text-institucional-oscuro">Mi horario</h2><p class="mt-2 text-sm text-slate-500">Visualizá tu planificación semanal.</p></div><div class="rounded-3xl bg-white p-6 shadow-sm"><span class="text-3xl">👥</span><h2 class="mt-8 text-xl font-bold text-institucional-oscuro">Estudiantes</h2><p class="mt-2 text-sm text-slate-500">Consultá los grupos asignados.</p></div></div>
@endsection
