<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Pantalla de Inicio de Sesión
Route::get('/', function () {
    return view('welcome');
})->name('login');

// Pantalla de Registro
Route::get('/registro', function () {
    return view('registro');
})->name('registro');

// Pantalla de Recuperación de Contraseña
Route::get('/recuperar', function () {
    return view('recuperar');
})->name('recuperar');

// Bolsa de Trabajo (Pública)
Route::get('/vacantes', function () {
    return view('vacantes');
})->name('vacantes.index');

// Empresas (Pública)
Route::get('/empresas', function () {
    return view('empresas');
})->name('empresas.index');


/*
|--------------------------------------------------------------------------
| RUTAS DE LOS DASHBOARDS (Protección vía JavaScript frontend)
|--------------------------------------------------------------------------
| Estas rutas se dejan libres del middleware web de Laravel porque 
| la autenticación se maneja vía Tokens (Sanctum) en el LocalStorage.
|--------------------------------------------------------------------------
*/

// Dashboard general
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Dashboard de Egresados
Route::get('/dashboard/egresado', function () {
    return view('dashboard');
})->name('dashboard.egresado');

// Dashboard de Empresa
Route::view('/dashboard/empresa', 'dashboard.empresa')
    ->name('dashboard.empresa');

// Dashboard de Administrador
Route::view('/dashboard/administrador', 'dashboard.admin')
    ->name('dashboard.admin');

// Alias compatible con redirecciones anteriores del frontend
Route::redirect('/dashboard/admin', '/dashboard/administrador');
