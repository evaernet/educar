@extends('layouts.app')
@section('titulo', 'Panel Alumno')

@section('contenido')
<div class="mb-8"><p class="text-sm font-semibold uppercase tracking-[.18em] text-institucional-turquesa">Espacio personal</p><h1 class="mt-1 text-3xl font-extrabold text-institucional-oscuro sm:text-4xl">Hola, {{ auth()->user()->name }}</h1><p class="mt-2 text-slate-500">Consultá tu información académica y mantenete al día.</p></div>
<div class="grid gap-5 md:grid-cols-3"><div class="rounded-3xl bg-gradient-to-br from-blue-600 to-cyan-500 p-6 text-white shadow-lg"><span class="text-4xl">🎒</span><h2 class="mt-8 text-xl font-bold">Mi perfil</h2><p class="mt-2 text-sm text-white/80">Tu información personal y académica.</p></div><div class="rounded-3xl bg-white p-6 shadow-sm"><span class="text-3xl">📚</span><h2 class="mt-8 text-xl font-bold text-institucional-oscuro">Mis materias</h2><p class="mt-2 text-sm text-slate-500">Próximamente vas a poder consultar cursadas y horarios.</p></div><div class="rounded-3xl bg-white p-6 shadow-sm"><span class="text-3xl">🏆</span><h2 class="mt-8 text-xl font-bold text-institucional-oscuro">Mis deportes</h2><p class="mt-2 text-sm text-slate-500">Consultá tus actividades deportivas inscriptas.</p></div></div>
@endsection
