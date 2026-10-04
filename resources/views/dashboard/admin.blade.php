@extends('layouts.app')
@section('titulo', 'Panel Admin')

@section('contenido')
<div class="mb-8"><p class="text-sm font-semibold uppercase tracking-[.18em] text-institucional-turquesa">Centro educativo</p><h1 class="mt-1 text-3xl font-extrabold text-institucional-oscuro sm:text-4xl">Panel de administración</h1><p class="mt-2 text-slate-500">Gestioná la información del centro educativo.</p></div>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-8">
    @foreach([['👥','Alumnos activos',$alumnosActivos,'text-blue-600'],['📚','Cursos',$cursos,'text-emerald-600'],['▣','Inscripciones activas',$inscripcionesActivas,'text-violet-600'],['🏆','Deportes',$deportes,'text-amber-600']] as [$icono,$etiqueta,$valor,$color])
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="flex items-center gap-4"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-2xl">{{ $icono }}</span><div><p class="text-sm font-medium text-slate-600">{{ $etiqueta }}</p><p class="text-3xl font-extrabold {{ $color }}">{{ $valor }}</p></div></div></div>
    @endforeach
</div>
<section class="rounded-3xl bg-white p-6 shadow-sm"><h2 class="text-xl font-bold text-institucional-oscuro">Accesos rápidos</h2><p class="mt-1 text-sm text-slate-500">Elegí una tarea para continuar.</p>
    @php $accesos = [
        ['👥','Gestionar alumnos','Altas, bajas y consultas', route('admin.alumnos.index'),'from-blue-600 to-cyan-500'],
        ['▤','Gestionar niveles','Organización educativa', route('admin.niveles.index'),'from-violet-600 to-indigo-500'],
        ['📚','Gestionar cursos','Cursos y divisiones', route('admin.cursos.index'),'from-orange-400 to-amber-500'],
        ['✦','Gestionar materias','Oferta académica', route('admin.materias.index'),'from-pink-500 to-rose-500'],
        ['▣','Asignaciones académicas','Materias y docentes', route('admin.asignaciones.index'),'from-indigo-600 to-blue-500'],
        ['📅','Ciclos lectivos','Períodos escolares', route('admin.ciclos.index'),'from-teal-600 to-emerald-500'],
        ['◫','Horarios','Planificación semanal', route('admin.horarios.index'),'from-cyan-600 to-sky-500'],
        ['✓','Inscripciones académicas','Asignación de cursos', route('admin.inscripciones.index'),'from-emerald-500 to-teal-500'],
        ['🏆','Deportes','Disciplinas y actividades', route('admin.deportes.index'),'from-amber-500 to-orange-500'],
        ['🍽','Comedor','Turnos, cupos e inscripciones', route('admin.comedor.index'),'from-rose-500 to-orange-400'],
        ['♙','Gestionar profesores','Equipo docente', route('admin.profesores.index'),'from-green-600 to-emerald-500'],
    ]; @endphp
    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($accesos as [$icono,$titulo,$descripcion,$ruta,$gradiente])
            <a href="{{ $ruta }}" class="group rounded-2xl bg-gradient-to-br {{ $gradiente }} p-5 text-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg"><div class="flex items-start justify-between"><span class="text-3xl">{{ $icono }}</span><span class="rounded-full bg-white/20 px-2 py-1 text-sm transition group-hover:translate-x-1">→</span></div><p class="mt-8 font-bold">{{ $titulo }}</p><p class="mt-1 text-sm text-white/80">{{ $descripcion }}</p></a>
        @endforeach
    </div>
</section>
@endsection
