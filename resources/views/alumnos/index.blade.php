@extends('layouts.app')

@section('titulo', 'Alumnos')

@section('contenido')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6"><div><a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-xl border border-cyan-100 bg-white px-3 py-2 text-sm font-semibold text-institucional-azul shadow-sm hover:bg-cyan-50">← Volver al panel</a><h1 class="mt-5 text-3xl font-extrabold text-institucional-oscuro">Alumnos</h1><p class="mt-1 text-slate-500">Administrá los registros de estudiantes.</p></div><a href="{{ route('admin.alumnos.create') }}" class="inline-flex items-center rounded-xl bg-gradient-to-r from-institucional-azul to-institucional-turquesa px-5 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5">+ Registrar alumno</a></div>
    <div class="overflow-x-auto rounded-3xl bg-white p-5 shadow-sm sm:p-6">
        @if (session('success'))
            <div role="status"
                class="mb-5 rounded-xl bg-emerald-50 p-4 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('admin.alumnos.index') }}"
            method="GET"
            class="mb-6 rounded-2xl bg-slate-50 p-4">

            <label for="buscar" class="mb-2 block text-sm font-semibold text-slate-700">
                Buscar por nombre, apellido o legajo
            </label>

            <div class="flex flex-wrap gap-2">
                <input
                    id="buscar"
                    name="buscar"
                    type="search"
                    value="{{ $buscar }}"
                    maxlength="100"
                    placeholder="Ingresá un nombre, apellido o legajo"
                    class="w-full sm:w-80 rounded-xl border border-slate-300 px-4 py-3"
                >

                <button type="submit"
                        class="rounded-xl bg-institucional-azul px-5 py-3 font-semibold text-white transition hover:bg-institucional-oscuro">
                    Buscar
                </button>

                <a href="{{ route('admin.alumnos.index') }}"
                class="rounded-xl border border-slate-200 bg-white px-5 py-3 font-semibold text-slate-700 hover:bg-slate-100">
                    Limpiar
                </a>
            </div>

    @error('buscar')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</form>
        <table class="w-full text-left">
            <thead class="bg-institucional-oscuro text-white">
                <tr>
                    <th scope="col" class="px-4 py-3">Legajo</th>
                    <th scope="col" class="px-4 py-3">DNI</th>
                    <th scope="col" class="px-4 py-3">Apellido</th>
                    <th scope="col" class="px-4 py-3">Nombre</th>
                    <th scope="col" class="px-4 py-3">Estado</th>
                    <th scope="col" class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($alumnos as $alumno)
                    <tr class="border-b border-slate-100 transition hover:bg-cyan-50/40">
                        <td class="px-4 py-3">{{ $alumno->legajo }}</td>
                        <td class="px-4 py-3">{{ $alumno->dni }}</td>
                        <td class="px-4 py-3">{{ $alumno->apellido }}</td>
                        <td class="px-4 py-3">{{ $alumno->nombre }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $alumno->activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $alumno->activo ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.alumnos.edit', $alumno) }}"
                            class="inline-flex rounded-lg bg-blue-50 px-3 py-2 text-sm font-semibold text-institucional-azul hover:bg-blue-100">
                                Editar
                            </a>

                            @if ($alumno->activo)
                                <form action="{{ route('admin.alumnos.destroy', $alumno) }}"
                                    method="POST"
                                    class="inline-block ml-2"
                                    onsubmit="return confirm('¿Confirmás dar de baja a este alumno?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                                        Dar de baja
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="px-4 py-8 text-center text-gray-500">
                            {{ $buscar !== ''
                                ? 'No se encontraron alumnos para esa búsqueda.'
                                : 'Todavía no hay alumnos registrados.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $alumnos->links() }}
    </div>
</div>
@endsection
