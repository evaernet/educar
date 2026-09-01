<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    // Cada función simplemente devuelve su vista correspondiente
    // La lógica de seguridad ya la hizo el middleware, no hace falta repetirla acá

    public function admin()
    {
        // view('dashboard.admin') busca el archivo
        // resources/views/dashboard/admin.blade.php
        return view('dashboard.admin');
    }

    public function profesor()
    {
        return view('dashboard.profesor');
    }

    public function alumno()
    {
        return view('dashboard.alumno');
    }

    public function padre()
    {
        return view('dashboard.padre');
    }
}