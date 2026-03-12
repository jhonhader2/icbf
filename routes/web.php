<?php

use App\Http\Controllers\ActivoCrvController;
use App\Http\Controllers\AllUsersImportController;
use App\Http\Controllers\CrvReporteImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TomaParqueImportController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\CpuController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MouseController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ParqueExportController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\TecladoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

const PROFILE_PATH = '/profile';

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get(PROFILE_PATH, [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch(PROFILE_PATH, [ProfileController::class, 'update'])->name('profile.update');
    Route::delete(PROFILE_PATH, [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('departments', DepartmentController::class);
    Route::get('personas/import', [AllUsersImportController::class, 'show'])->name('personas.import');
    Route::post('personas/import', [AllUsersImportController::class, 'store'])->name('personas.import.store');
    Route::post('personas/{persona}/crear-usuario', [PersonaController::class, 'crearUsuario'])->name('personas.crear-usuario');
    Route::post('personas/crear-usuarios-masivo', [PersonaController::class, 'crearUsuariosMasivo'])->name('personas.crear-usuarios-masivo');
    Route::post('personas/crear-usuarios-todos', [PersonaController::class, 'crearUsuariosTodos'])->name('personas.crear-usuarios-todos');
    Route::resource('personas', PersonaController::class);
    Route::resource('regionales', RegionalController::class)->parameters(['regionales' => 'regional']);
    Route::resource('bodegas', BodegaController::class);
    Route::resource('cpus', CpuController::class);
    Route::get('toma-parque/import', [TomaParqueImportController::class, 'show'])->name('toma-parque.import');
    Route::post('toma-parque/import', [TomaParqueImportController::class, 'store'])->name('toma-parque.import.store');
    Route::resource('monitores', MonitorController::class)->parameters(['monitores' => 'monitor']);
    Route::resource('teclados', TecladoController::class);
    Route::resource('mice', MouseController::class)->parameters(['mice' => 'mouse']);
    Route::get('parque/export', ParqueExportController::class)->name('parque.export');
    Route::get('crv/import', [CrvReporteImportController::class, 'show'])->name('crv.import');
    Route::post('crv/import', [CrvReporteImportController::class, 'store'])->name('crv.import.store');
    Route::resource('productos', ProductoController::class)->parameters(['productos' => 'producto']);
    Route::resource('activos-crv', ActivoCrvController::class)->parameters(['activos-crv' => 'activoCrv']);
});

require __DIR__.'/auth.php';
