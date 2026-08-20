<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VacanteController;
use App\Http\Controllers\EmpresaController; // Importamos el nuevo controlador
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmpresaDashboardController;
use App\Http\Controllers\PostulacionController;
use App\Http\Middleware\CheckRole; // <-- Importación del middleware de roles

// Modificamos el middleware del grupo para exigir el rol 'administrador'
Route::middleware(['auth:sanctum', CheckRole::class.':admin'])->prefix('admin')->group(function () {
    Route::get('/empresas', [AdminController::class, 'getEmpresas']);
    Route::get('/egresados', [AdminController::class, 'getEgresados']);
    Route::get('/usuarios-aceptados', [AdminController::class, 'usuariosAceptados']);
    Route::post('/crear-representante', [AdminController::class, 'crearRepresentante']);
    Route::put('/usuarios/{id}', [AdminController::class, 'actualizarUsuario']);
});

Route::middleware(['auth:sanctum', CheckRole::class.':empresa'])->prefix('empresa')->group(function () {
    Route::get('/dashboard', [EmpresaDashboardController::class, 'dashboard']);
    Route::get('/perfil', [EmpresaDashboardController::class, 'perfil']);
    Route::put('/perfil', [EmpresaDashboardController::class, 'actualizarPerfil']);
    Route::get('/vacantes', [EmpresaDashboardController::class, 'vacantes']);
    Route::get('/solicitantes', [EmpresaDashboardController::class, 'solicitantes']);
    Route::post('/vacantes', [EmpresaDashboardController::class, 'publicarVacante']);
    Route::put('/vacantes/{vacante}', [EmpresaDashboardController::class, 'actualizarVacante']);
    Route::put('/postulaciones/{postulacion}/estado', [EmpresaDashboardController::class, 'actualizarEstadoPostulacion']);
});

// Rutas para obtener datos (Públicas o accesibles para la vista)
Route::get('/vacantes', [VacanteController::class, 'index']);
Route::middleware(['auth:sanctum', CheckRole::class.':egresado'])->post('/vacantes/{vacante}/postular', [PostulacionController::class, 'store']);
Route::middleware(['auth:sanctum', CheckRole::class.':egresado'])->prefix('egresado')->group(function () {
    Route::get('/perfil', [DashboardController::class, 'perfilEgresado']);
    Route::post('/perfil', [DashboardController::class, 'actualizarPerfilEgresado']);
});
Route::get('/empresas', [EmpresaController::class, 'index']); // Nueva ruta para alimentar la vista de empresas

// Rutas públicas a las que cualquiera puede acceder sin estar logueado
Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/solicitar-recuperacion', [AuthController::class, 'solicitarRecuperacion']);
Route::post('/restablecer-contrasena', [AuthController::class, 'restablecerContrasena']);

// Agrega esto donde tengas tus rutas protegidas con auth:sanctum
Route::middleware('auth:sanctum')->get('/dashboard/metricas', [DashboardController::class, 'obtenerMetricas']);
