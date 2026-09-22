{{-- @extends indica que esta vista usa como base el layout general
     (el menú, el header, etc. están definidos ahí una sola vez) --}}
@extends('layouts.app')

{{-- Esto llena el título de la pestaña/página --}}
@section('titulo', 'Profesores')

{{-- Todo lo que va dentro de @section('contenido') / @endsection
     es lo que se inserta en el "hueco" del layout --}}
@section('contenido')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('admin.dashboard') }}"
       class="text-blue-700 hover:underline">
        Volver al panel
    </a>

    <h1 class="text-3xl font-bold text-gray-800 my-6">
        Profesores
    </h1>

    <a href="{{ route('admin.profesores.create') }}"
        class="inline-block bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded mb-4">
         Registrar profesor
    </a>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        {{-- session('success') es el mensaje que dejamos con ->with('success', ...)
             en el controlador, cuando redirigimos después de guardar/editar/borrar --}}
        @if (session('success'))
            <div role="status"
                class="bg-green-100 text-green-800 rounded p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulario de búsqueda: method="GET" porque solo estamos
             consultando datos, no modificando nada --}}
        <form action="{{ route('admin.profesores.index') }}"
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

                <a href="{{ route('admin.profesores.index') }}"
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
                    <th scope="col" class="px-4 py-3">Apellido</th>
                    <th scope="col" class="px-4 py-3">Nombre</th>
                    <th scope="col" class="px-4 py-3">Especialidad</th>
                    <th scope="col" class="px-4 py-3">Estado</th>
                    <th scope="col" class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- @forelse recorre $profesores (lo que armamos en index() del
                     controlador). Si viene vacío, muestra el bloque @empty --}}
                @forelse ($profesores as $profesor)
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $profesor->legajo }}</td>
                        <td class="px-4 py-3">{{ $profesor->apellido }}</td>
                        <td class="px-4 py-3">{{ $profesor->nombre }}</td>
                        <td class="px-4 py-3">{{ $profesor->especialidad }}</td>
                        <td class="px-4 py-3">
                            {{ $profesor->activo ? 'Activo' : 'Inactivo' }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.profesores.edit', $profesor) }}"
                            class="text-blue-700 hover:underline">
                                Editar
                            </a>

                            @if ($profesor->activo)
                                {{-- method="POST" + @method('DELETE') es el truco
                                     que usa Laravel: los navegadores solo mandan
                                     GET/POST desde un <form>, así que se "disfraza"
                                     el POST como DELETE con este campo oculto --}}
                                <form action="{{ route('admin.profesores.destroy', $profesor) }}"
                                    method="POST"
                                    class="inline-block ml-3"
                                    onsubmit="return confirm('¿Confirmás dar de baja a este profesor?');">
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
                            {{ $buscar
                                ? 'No se encontraron profesores para esa búsqueda.'
                                : 'Todavía no hay profesores registrados.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- $profesores->links() dibuja automáticamente los botones de
         "página 1, 2, 3..." que genera el paginate(10) del controlador --}}
    <div class="mt-4">
        {{ $profesores->links() }}
    </div>
</div>
@endsection