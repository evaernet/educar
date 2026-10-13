@extends('layouts.app')
@section('titulo','Cuotas y facturación')
@section('contenido')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('admin.dashboard') }}" class="inline-flex rounded-xl border border-cyan-100 bg-white px-3 py-2 font-semibold text-institucional-azul">← Volver al panel</a>
    <h1 class="mt-5 text-3xl font-extrabold text-institucional-oscuro">Cuotas y facturación</h1>
    <p class="mt-1 text-slate-500">Fijá los valores y generá las facturas del mes.</p>
    @if(session('success'))<div class="mt-5 rounded-xl bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mt-5 rounded-xl bg-red-50 p-4 text-red-700">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('admin.cuotas.valores') }}" class="mt-6 rounded-3xl bg-white p-6 shadow-sm space-y-4">
        @csrf
        <h2 class="text-xl font-bold text-institucional-oscuro">Valores mensuales</h2>
        <div class="grid gap-3 md:grid-cols-2">
            <label class="block text-sm font-semibold text-slate-600">Cuota mensual ($)
                <input type="number" step="0.01" min="0" name="cuota" value="{{ old('cuota', $cuota) }}" required class="mt-1 w-full rounded-xl border p-3"></label>
            <label class="block text-sm font-semibold text-slate-600">Comedor ($)
                <input type="number" step="0.01" min="0" name="comedor" value="{{ old('comedor', $comedor) }}" required class="mt-1 w-full rounded-xl border p-3"></label>
        </div>

        <h3 class="font-bold text-institucional-oscuro">Deportes</h3>
        @forelse($deportes as $d)
            <div class="grid items-center gap-3 md:grid-cols-3">
                <span class="font-semibold">{{ $d->nombre }}</span>
                <input type="number" step="0.01" min="0" name="deportes[{{ $d->id }}][valor_mensual]" value="{{ $d->valor_mensual }}" required class="rounded-xl border p-3" placeholder="Valor mensual">
                <select name="deportes[{{ $d->id }}][profesor_id]" class="rounded-xl border p-3">
                    <option value="">Sin profesor a cargo</option>
                    @foreach($profesores as $p)<option value="{{ $p->id }}" @selected($d->profesor_id == $p->id)>{{ $p->apellido }}, {{ $p->nombre }}</option>@endforeach
                </select>
            </div>
        @empty<p class="text-slate-500">No hay deportes activos.</p>@endforelse

        <h3 class="font-bold text-institucional-oscuro">Recorridos de transporte</h3>
        @forelse($recorridos as $r)
            <div class="grid items-center gap-3 md:grid-cols-3">
                <span class="font-semibold">{{ $r->nombre }}</span>
                <input type="number" step="0.01" min="0" name="recorridos[{{ $r->id }}][valor_mensual]" value="{{ $r->valor_mensual }}" required class="rounded-xl border p-3" placeholder="Valor mensual">
            </div>
        @empty<p class="text-slate-500">No hay recorridos activos.</p>@endforelse

        <button class="rounded-xl bg-gradient-to-r from-institucional-azul to-institucional-turquesa px-5 py-3 font-semibold text-white">Guardar valores</button>
    </form>

    <form method="POST" action="{{ route('admin.cuotas.generar') }}" class="mt-6 flex flex-wrap items-end gap-3 rounded-3xl bg-white p-6 shadow-sm">
        @csrf
        <label class="text-sm font-semibold text-slate-600">Periodo a facturar
            <input type="month" name="periodo" value="{{ $periodo }}" required class="mt-1 block rounded-xl border p-3"></label>
        <button class="rounded-xl bg-institucional-azul px-5 py-3 font-semibold text-white">Generar facturas</button>
        <a href="{{ route('admin.cuotas.index', ['periodo' => $periodo]) }}" class="rounded-xl border px-5 py-3 font-semibold">Ver periodo</a>
    </form>

    <div class="mt-6 overflow-x-auto rounded-3xl bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-xl font-bold text-institucional-oscuro">Facturas del periodo {{ $periodo }}</h2>
        <table class="w-full"><thead class="bg-institucional-oscuro text-white"><tr><th class="p-3 text-left">Alumno</th><th class="p-3 text-left">Vence</th><th class="p-3 text-right">Total</th><th class="p-3">Estado</th></tr></thead><tbody>
        @forelse($facturas as $fa)
            <tr class="border-b"><td class="p-3">{{ $fa->alumno->apellido }}, {{ $fa->alumno->nombre }}</td><td class="p-3">{{ $fa->fecha_vencimiento->format('d/m/Y') }}</td><td class="p-3 text-right">${{ number_format((float)$fa->total,2,',','.') }}</td><td class="p-3 text-center">{{ ucfirst($fa->estado) }}</td></tr>
        @empty<tr><td colspan="4" class="p-6 text-center text-slate-500">No hay facturas en este periodo.</td></tr>@endforelse
        </tbody></table>
    </div>
</div>
@endsection
