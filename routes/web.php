<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\AsignacionAcademicaController;
use App\Http\Controllers\CicloLectivoController;
use App\Http\Controllers\HorarioClaseController;
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
Route::post('/login', [LoginController::class, 'procesar'])
    ->middleware('throttle:login')
    ->name('login.procesar');

// POST /logout → cierra la sesión con protección CSRF
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// GET /register → muestra el formulario de registro
Route::get('/register', [RegisterController::class, 'mostrarFormulario'])->name('register');

// POST /register → procesa el registro
Route::post('/register', [RegisterController::class, 'procesar'])
    ->middleware('throttle:register')
    ->name('register.procesar');


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

Route::resource('admin/niveles', NivelController::class)->parameters(['niveles'=>'nivel'])->names('admin.niveles')->middleware(['auth','role:admin']);
Route::resource('admin/cursos', CursoController::class)->parameters(['cursos'=>'curso'])->names('admin.cursos')->middleware(['auth','role:admin']);
Route::resource('admin/materias', MateriaController::class)->parameters(['materias'=>'materia'])->names('admin.materias')->middleware(['auth','role:admin']);
Route::get('/admin/asignaciones', [AsignacionAcademicaController::class, 'index'])->middleware(['auth','role:admin'])->name('admin.asignaciones.index');
Route::get('/admin/asignaciones/create', [AsignacionAcademicaController::class, 'create'])->middleware(['auth','role:admin'])->name('admin.asignaciones.create');
Route::post('/admin/asignaciones', [AsignacionAcademicaController::class, 'store'])->middleware(['auth','role:admin'])->name('admin.asignaciones.store');
Route::get('/admin/asignaciones/{asignacion}/edit', [AsignacionAcademicaController::class, 'edit'])->middleware(['auth','role:admin'])->name('admin.asignaciones.edit');
Route::put('/admin/asignaciones/{asignacion}', [AsignacionAcademicaController::class, 'update'])->middleware(['auth','role:admin'])->name('admin.asignaciones.update');
Route::delete('/admin/asignaciones/{asignacion}', [AsignacionAcademicaController::class, 'destroy'])->middleware(['auth','role:admin'])->name('admin.asignaciones.destroy');
Route::resource('admin/ciclos', CicloLectivoController::class)->parameters(['ciclos'=>'ciclo'])->names('admin.ciclos')->middleware(['auth','role:admin']);
Route::resource('admin/horarios', HorarioClaseController::class)->parameters(['horarios'=>'horario'])->names('admin.horarios')->middleware(['auth','role:admin']);
Route::resource('admin/profesores', ProfesorController::class)
    ->parameters(['profesores' => 'profesor'])
    ->names('admin.profesores')
    ->middleware(['auth', 'role:admin']);
