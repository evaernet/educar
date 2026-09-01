<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- @yield('titulo') es un "hueco" que cada vista va a llenar --}}
    <title>@yield('titulo', 'Educar Para Transformar')</title>

    {{-- Tailwind CSS desde CDN, no necesitás instalarlo --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Barra de navegación (solo se muestra si el usuario está logueado) --}}
    @auth
        <nav class="bg-blue-800 text-white px-6 py-3 flex justify-between items-center">
            <span class="font-bold text-lg">🏫 Educar Para Transformar</span>
            <div class="flex items-center gap-4">
                {{-- auth()->user() devuelve el usuario logueado --}}
                <span>Hola, {{ auth()->user()->name }}</span>
                <a href="{{ route('logout') }}"
                   class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm">
                    Cerrar sesión
                </a>
            </div>
        </nav>
    @endauth

    {{-- Contenido principal: cada vista pone lo suyo acá --}}
    <main class="p-6">
        {{-- @yield('contenido') es otro hueco para el contenido de cada página --}}
        @yield('contenido')
    </main>

</body>
</html>