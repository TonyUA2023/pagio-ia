<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ruta de bienvenida pública
Route::get('/', function () {
    return view('welcome');
});

// Ruta del panel de control (Dashboard)
Route::get('/dashboard', [DocumentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grupo de rutas que requieren que el usuario esté autenticado
Route::middleware('auth')->group(function () {
    // Rutas para la gestión del perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ruta para procesar la subida de nuevos documentos
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

    // --- RUTA QUE FALTABA ---
    // Ruta para iniciar el análisis de un documento específico
    Route::post('/documents/{document}/analyze', [DocumentController::class, 'analyze'])->name('documents.analyze');

    // Rutas para los reportes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/documents/{document}/status', [App\Http\Controllers\DocumentController::class, 'status'])->name('documents.status');

});

// Incluye las rutas de autenticación de Breeze
require __DIR__.'/auth.php';