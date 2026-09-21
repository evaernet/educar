<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ProfesorController;

// ============================================================
// RUTAS PÚBLICAS — cualquiera puede entrar, sin estar logueado
// ============================================================

// Ruta raíz: si entrás a "/" te manda directo al login
Route::get('/', function () {
    return redirect()->route('login');
});

// GET /login → muestra el formulario de login
Route::get('/login', [LoginController::class, 'mostrarFormulario'])->name('login');

// POST /login → procesa el formulario cuando apretás "Entrar"
// GET y POST son métodos HTTP. GET = pedir una página. POST = enviar datos.
Route::post('/login', [LoginController::class, 'procesar'])->name('login.procesar');

// GET /logout → cierra la sesión
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// GET /register → muestra el formulario de registro
Route::get('/register', [RegisterController::class, 'mostrarFormulario'])->name('register');

// POST /register → procesa el registro
Route::post('/register', [RegisterController::class, 'procesar'])->name('register.procesar');


// ============================================================
// RUTAS PRIVADAS — solo para usuarios logueados
// El middleware 'auth' verifica que estés logueado
// Si no estás logueado, te manda al login automáticamente
// ============================================================

// Dashboard del Administrador
// El segundo middleware 'role:admin' verifica que tu rol sea "admin"
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

// Dashboard del Profesor
Route::get('/profesor/dashboard', [DashboardController::class, 'profesor'])
    ->middleware(['auth', 'role:profesor'])
    ->name('profesor.dashboard');

// Dashboard del Alumno
Route::get('/alumno/dashboard', [DashboardController::class, 'alumno'])
    ->middleware(['auth', 'role:alumno'])
    ->name('alumno.dashboard');

// Dashboard del Padre
Route::get('/padre/dashboard', [DashboardController::class, 'padre'])
    ->middleware(['auth', 'role:padre'])
    ->name('padre.dashboard');

Route::get('/admin/alumnos', [AlumnoController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.alumnos.index');

Route::get('/admin/alumnos/create', [AlumnoController::class, 'create'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.alumnos.create');

Route::post('/admin/alumnos', [AlumnoController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.alumnos.store');

Route::get('/admin/alumnos/{alumno}/edit', [AlumnoController::class, 'edit'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.alumnos.edit');

Route::put('/admin/alumnos/{alumno}', [AlumnoController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.alumnos.update');

Route::delete('/admin/alumnos/{alumno}', [AlumnoController::class, 'destroy'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.alumnos.destroy');

// ============================================================
// RUTAS DE PROFESORES
//
// ¿Qué hacen?: Cada línea conecta una URL + método HTTP (GET/POST/PUT/DELETE)
// con un método específico de ProfesorController. Es el mapa que le dice a
// Laravel "cuando entren a tal dirección, ejecutá tal función".
// Conexión: Mismo patrón que las rutas de alumnos de arriba, cambiando
// AlumnoController por ProfesorController.
// ============================================================

// GET /admin/profesores -> llama a index(): muestra la lista con buscador
Route::get('/admin/profesores', [ProfesorController::class, 'index'])
    ->middleware(['auth', 'role:admin']) // solo entra si está logueado Y es admin
    ->name('admin.profesores.index');    // nombre que usamos en los redirect() del controlador

// GET /admin/profesores/create -> llama a create(): muestra el formulario vacío
Route::get('/admin/profesores/create', [ProfesorController::class, 'create'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profesores.create');

// POST /admin/profesores -> llama a store(): guarda lo que mandó el formulario
Route::post('/admin/profesores', [ProfesorController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profesores.store');

// GET /admin/profesores/{profesor}/edit -> llama a edit(): formulario precargado
// {profesor} es el id del profesor; Laravel lo convierte automáticamente en
// el objeto Profesor completo (route model binding), como vimos en el controlador.
Route::get('/admin/profesores/{profesor}/edit', [ProfesorController::class, 'edit'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profesores.edit');

// PUT /admin/profesores/{profesor} -> llama a update(): guarda los cambios
Route::put('/admin/profesores/{profesor}', [ProfesorController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profesores.update');

// DELETE /admin/profesores/{profesor} -> llama a destroy(): baja lógica (activo = false)
Route::delete('/admin/profesores/{profesor}', [ProfesorController::class, 'destroy'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profesores.destroy');