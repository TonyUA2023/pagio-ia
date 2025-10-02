<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController; // ¡Importante tener esta línea!
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

// -- ESTA ES LA RUTA CORREGIDA --
// Ahora, el DocumentController maneja la vista del dashboard para poder pasarle datos.
Route::get('/dashboard', [DocumentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grupo de rutas que requieren autenticación
Route::middleware('auth')->group(function () {
    // Rutas para la gestión del perfil de usuario (generadas por Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ruta para procesar la subida de documentos
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
});

// Incluye las rutas de autenticación (login, register, etc.) generadas por Breeze
require __DIR__.'/auth.php';

