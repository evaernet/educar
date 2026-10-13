@extends('layouts.app')
@section('titulo','Reportes de pagos')
@section('contenido')
@php $m = fn($v) => '$'.number_format((float)$v,2,',','.'); @endphp
<div class="max-w-6xl mx-auto">
    <a href="{{ route('admin.dashboard') }}" class="inline-flex rounded-xl border border-cyan-100 bg-white px-3 py-2 font-semibold text-institucional-azul">← Volver al panel</a>
    <h1 class="mt-5 text-3xl font-extrabold text-institucional-oscuro">Reportes de pagos</h1>

    <form method="GET" class="mt-6 grid gap-3 rounded-3xl bg-white p-6 shadow-sm md:grid-cols-4">
        <label class="text-sm font-semibold text-slate-600">Desde<input type="date" name="desde" value="{{ $f['desde'] ?? '' }}" class="mt-1 w-full rounded-xl border p-3"></label>
        <label class="text-sm font-semibold text-slate-600">Hasta<input type="date" name="hasta" value="{{ $f['hasta'] ?? '' }}" class="mt-1 w-full rounded-xl border p-3"></label>
        <label class="text-sm font-semibold text-slate-600">Año<input type="number" name="anio" value="{{ $f['anio'] ?? '' }}" class="mt-1 w-full rounded-xl border p-3" placeholder="2026"></label>
        <label class="text-sm font-semibold text-slate-600">Alumno<select name="alumno_id" class="mt-1 w-full rounded-xl border p-3"><option value="">Todos</option>@foreach($alumnos as $a)<option value="{{ $a->id }}" @selected(($f['alumno_id'] ?? '') == $a->id)>{{ $a->apellido }}, {{ $a->nombre }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold text-slate-600">Deporte<select name="deporte_id" class="mt-1 w-full rounded-xl border p-3"><option value="">Todos</option>@foreach($deportes as $d)<option value="{{ $d->id }}" @selected(($f['deporte_id'] ?? '') == $d->id)>{{ $d->nombre }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold text-slate-600">Nivel<select name="nivel_id" class="mt-1 w-full rounded-xl border p-3"><option value="">Todos</option>@foreach($niveles as $n)<option value="{{ $n->id }}" @selected(($f['nivel_id'] ?? '') == $n->id)>{{ $n->nombre }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold text-slate-600">Horario (día)<select name="dia_semana" class="mt-1 w-full rounded-xl border p-3"><option value="">Todos</option>@foreach($dias as $k => $n)<option value="{{ $k }}" @selected(($f['dia_semana'] ?? '') == $k)>{{ $n }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold text-slate-600">Profesor<select name="profesor_id" class="mt-1 w-full rounded-xl border p-3"><option value="">Todos</option>@foreach($profesores as $p)<option value="{{ $p->id }}" @selected(($f['profesor_id'] ?? '') == $p->id)>{{ $p->apellido }}, {{ $p->nombre }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold text-slate-600">Recorrido<select name="recorrido_id" class="mt-1 w-full rounded-xl border p-3"><option value="">Todos</option>@foreach($recorridos as $r)<option value="{{ $r->id }}" @selected(($f['recorrido_id'] ?? '') == $r->id)>{{ $r->nombre }}</option>@endforeach</select></label>
        <div class="flex items-end gap-2 md:col-span-3"><button class="rounded-xl bg-institucional-azul px-5 py-3 font-semibold text-white">Filtrar</button><a href="{{ route('admin.reportes.pagos') }}" class="rounded-xl border px-5 py-3 font-semibold">Limpiar</a></div>
    </form>
    @if($errors->any())<div class="mt-5 rounded-xl bg-red-50 p-4 text-red-700">{{ $errors->first() }}</div>@endif

    <div class="mt-6 overflow-x-auto rounded-3xl bg-white p-5 shadow-sm">
        <h2 class="mb-1 text-xl font-bold text-institucional-oscuro">Ingresos por período</h2>
        <p class="mb-4 text-slate-500">Total cobrado: <strong>{{ $m($totalIngresos) }}</strong></p>
        <table class="w-full"><thead class="bg-institucional-oscuro text-white"><tr><th class="p-3 text-left">Mes</th><th class="p-3">Pagos</th><th class="p-3 text-right">Total</th></tr></thead><tbody>
        @forelse($ingresos as $i)<tr class="border-b"><td class="p-3">{{ $i['mes'] }}</td><td class="p-3 text-center">{{ $i['cantidad'] }}</td><td class="p-3 text-right">{{ $m($i['total']) }}</td></tr>
        @empty<tr><td colspan="3" class="p-6 text-center text-slate-500">Sin pagos en el período.</td></tr>@endforelse</tbody></table>
    </div>

    @foreach([['Pagos completos', $completas], ['Pagos incompletos', $incompletas]] as [$titulo, $lista])
    <div class="mt-6 overflow-x-auto rounded-3xl bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-xl font-bold text-institucional-oscuro">{{ $titulo }} ({{ $lista->count() }})</h2>
        <table class="w-full"><thead class="bg-institucional-oscuro text-white"><tr><th class="p-3 text-left">Alumno</th><th class="p-3">Periodo</th><th class="p-3 text-right">Total</th><th class="p-3 text-right">Adeudado</th></tr></thead><tbody>
        @forelse($lista as $fa)<tr class="border-b"><td class="p-3">{{ $fa->alumno->apellido }}, {{ $fa->alumno->nombre }}</td><td class="p-3 text-center">{{ $fa->periodo }}</td><td class="p-3 text-right">{{ $m($fa->total) }}</td><td class="p-3 text-right">{{ $m($fa->items->whereNull('pago_id')->sum('importe')) }}</td></tr>
        @empty<tr><td colspan="4" class="p-6 text-center text-slate-500">Sin registros.</td></tr>@endforelse</tbody></table>
    </div>
    @endforeach

    <div class="mt-6 overflow-x-auto rounded-3xl bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-xl font-bold text-institucional-oscuro">Pagos por deporte</h2>
        <table class="w-full"><thead class="bg-institucional-oscuro text-white"><tr><th class="p-3 text-left">Alumno</th><th class="p-3 text-left">Deporte</th><th class="p-3 text-left">Nivel</th><th class="p-3 text-left">Horario</th><th class="p-3 text-left">Profesor</th><th class="p-3 text-right">Importe</th></tr></thead><tbody>
        @forelse($porDeporte as $it)
            @php $al = $it->factura->alumno; $niv = $al->inscripciones->where('activo', true)->first()?->curso?->nivel?->nombre; @endphp
            <tr class="border-b"><td class="p-3">{{ $al->apellido }}, {{ $al->nombre }}</td><td class="p-3">{{ $it->deporte?->nombre }}</td><td class="p-3">{{ $niv ?? '—' }}</td>
            <td class="p-3">{{ $it->deporte?->horarios->map(fn($h) => ($dias[$h->dia_semana] ?? '').' '.substr($h->hora_inicio,0,5).'-'.substr($h->hora_fin,0,5))->implode(', ') ?: '—' }}</td>
            <td class="p-3">{{ $it->deporte?->profesor ? $it->deporte->profesor->apellido.', '.$it->deporte->profesor->nombre : '—' }}</td><td class="p-3 text-right">{{ $m($it->importe) }}</td></tr>
        @empty<tr><td colspan="6" class="p-6 text-center text-slate-500">Sin pagos de deportes.</td></tr>@endforelse</tbody></table>
    </div>

    <div class="mt-6 overflow-x-auto rounded-3xl bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-xl font-bold text-institucional-oscuro">Pagos por recorrido de transporte</h2>
        <table class="w-full"><thead class="bg-institucional-oscuro text-white"><tr><th class="p-3 text-left">Recorrido</th><th class="p-3">Pagos</th><th class="p-3 text-right">Total</th></tr></thead><tbody>
        @forelse($porRecorrido as $r)<tr class="border-b"><td class="p-3">{{ $r['recorrido'] }}</td><td class="p-3 text-center">{{ $r['cantidad'] }}</td><td class="p-3 text-right">{{ $m($r['total']) }}</td></tr>
        @empty<tr><td colspan="3" class="p-6 text-center text-slate-500">Sin pagos de transporte.</td></tr>@endforelse</tbody></table>
    </div>
</div>
@endsection
