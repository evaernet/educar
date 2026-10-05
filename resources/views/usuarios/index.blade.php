@extends('layouts.app')

@section('titulo', 'Usuarios')

@section('contenido')
<div class="mx-auto max-w-6xl">
    <a href="{{ route('admin.dashboard') }}" class="inline-flex rounded-xl border border-cyan-100 bg-white px-3 py-2 font-semibold text-institucional-azul">
        ← Volver al panel
    </a>

    <div class="mt-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-3xl font-extrabold text-institucional-oscuro">Usuarios</h1>
            <p class="mt-1 text-slate-500">Administrá cuentas y permisos de acceso.</p>
        </div>
        <div class="rounded-2xl bg-cyan-50 px-4 py-3 text-sm font-semibold text-institucional-oscuro">
            {{ $usuarios->count() }} cuentas registradas
        </div>
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-xl bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-5 rounded-xl bg-red-50 p-4 text-red-800">
            <p class="font-semibold">Revisá los datos ingresados.</p>
            <ul class="mt-1 list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="mt-6 overflow-x-auto rounded-3xl bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-institucional-oscuro">Cuentas registradas</h2>
            <a href="#crear-usuario" class="rounded-xl bg-gradient-to-r from-institucional-azul to-institucional-turquesa px-4 py-2 text-sm font-semibold text-white">
                Crear usuario
            </a>
        </div>

        <table class="w-full">
            <thead class="bg-institucional-oscuro text-white">
                <tr>
                    <th class="p-3 text-left">Nombre</th>
                    <th class="p-3 text-left">Correo</th>
                    <th class="p-3">Rol</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr class="border-b border-slate-100">
                        <td class="p-3">{{ $usuario->name }}</td>
                        <td class="p-3">{{ $usuario->email }}</td>
                        <td class="p-3 text-center capitalize">{{ $usuario->role }}</td>
                        <td class="p-3 text-center">
                            <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $usuario->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <form method="POST" action="{{ route('admin.usuarios.estado', $usuario) }}">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-lg px-3 py-2 text-sm font-semibold {{ $usuario->activo ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ $usuario->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-500">Todavía no hay cuentas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section id="crear-usuario" class="mt-6 rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-institucional-oscuro">Crear una cuenta</h2>
        <form method="POST" action="{{ route('admin.usuarios.store') }}" class="mt-4 grid gap-3 md:grid-cols-2">
            @csrf
            <input name="name" value="{{ old('name') }}" required placeholder="Nombre completo" class="rounded-xl border border-slate-200 p-3">
            <input name="email" value="{{ old('email') }}" type="email" required placeholder="Correo" class="rounded-xl border border-slate-200 p-3">
            <select name="role" required class="rounded-xl border border-slate-200 p-3">
                <option value="">Rol</option>
                <option value="admin" @selected(old('role') === 'admin')>Administrador</option>
                <option value="padre" @selected(old('role') === 'padre')>Padre / tutor</option>
            </select>
            <input name="password" type="password" required placeholder="Contraseña segura" class="rounded-xl border border-slate-200 p-3">
            <input name="password_confirmation" type="password" required placeholder="Repetir contraseña" class="rounded-xl border border-slate-200 p-3">
            <button class="rounded-xl bg-gradient-to-r from-institucional-azul to-institucional-turquesa px-5 py-3 font-semibold text-white">
                Crear usuario
            </button>
        </form>
    </section>
</div>
@endsection
