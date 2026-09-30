@extends('layouts.app')
@section('titulo', 'Panel Padre')

@section('contenido')
<div class="mb-8"><p class="text-sm font-semibold uppercase tracking-[.18em] text-institucional-turquesa">Espacio de familias</p><h1 class="mt-1 text-3xl font-extrabold text-institucional-oscuro sm:text-4xl">Hola, {{ auth()->user()->name }}</h1><p class="mt-2 text-slate-500">Acompañá el recorrido escolar de tus hijos.</p></div>
<div class="grid gap-5 md:grid-cols-3"><div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-500 p-6 text-white shadow-lg"><span class="text-4xl">👨‍👩‍👧</span><h2 class="mt-8 text-xl font-bold">Mis hijos</h2><p class="mt-2 text-sm text-white/80">Información académica vinculada a tu familia.</p></div><div class="rounded-3xl bg-white p-6 shadow-sm"><span class="text-3xl">📅</span><h2 class="mt-8 text-xl font-bold text-institucional-oscuro">Actividades</h2><p class="mt-2 text-sm text-slate-500">Próximamente podrás ver eventos y horarios.</p></div><div class="rounded-3xl bg-white p-6 shadow-sm"><span class="text-3xl">✉</span><h2 class="mt-8 text-xl font-bold text-institucional-oscuro">Comunicaciones</h2><p class="mt-2 text-sm text-slate-500">Mensajes y novedades del centro educativo.</p></div></div>
@endsection
