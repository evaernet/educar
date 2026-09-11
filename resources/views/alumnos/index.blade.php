@extends('layouts.app')

@section('titulo', 'Alumnos')

@section('contenido')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('admin.dashboard') }}"
       class="text-blue-700 hover:underline">
        Volver al panel
    </a>

    <h1 class="text-3xl font-bold text-gray-800 my-6">
        Alumnos
    </h1>
    <a href="{{ route('admin.alumnos.create') }}"
        class="inline-block bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded mb-4">
         Registrar alumno
    </a>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        @if (session('success'))
            <div role="status"
                class="bg-green-100 text-green-800 rounded p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('admin.alumnos.index') }}"
            method="GET"
            class="mb-6">

            <label for="buscar" class="block text-gray-700 font-medium mb-2">
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
                    class="w-full sm:w-80 border border-gray-300 rounded px-3 py-2"
                >

                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">
                    Buscar
                </button>

                <a href="{{ route('admin.alumnos.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                    Limpiar
                </a>
            </div>

    @error('buscar')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</form>
        <table class="w-full text-left">
            <thead class="bg-blue-800 text-white">
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
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $alumno->legajo }}</td>
                        <td class="px-4 py-3">{{ $alumno->dni }}</td>
                        <td class="px-4 py-3">{{ $alumno->apellido }}</td>
                        <td class="px-4 py-3">{{ $alumno->nombre }}</td>
                        <td class="px-4 py-3">
                            {{ $alumno->activo ? 'Activo' : 'Inactivo' }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.alumnos.edit', $alumno) }}"
                            class="text-blue-700 hover:underline">
                                Editar
                            </a>

                            @if ($alumno->activo)
                                <form action="{{ route('admin.alumnos.destroy', $alumno) }}"
                                    method="POST"
                                    class="inline-block ml-3"
                                    onsubmit="return confirm('¿Confirmás dar de baja a este alumno?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-700 hover:underline">
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