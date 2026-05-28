<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DocenteInscripcionController;
use App\Http\Controllers\API\CursoController;
use App\Http\Controllers\API\VisualizacionCursoController;
use App\Http\Controllers\API\InscripcionController;
use App\Http\Controllers\API\PerfilController;
use App\Http\Controllers\API\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/perfil', [AuthController::class, 'profile']);
    });
});

Route::prefix('usuarios')
    ->middleware(['auth:sanctum', 'admin.role'])
    ->group(function () {
        Route::get('/', [UsuarioController::class, 'index']);
        Route::get('/form-data', [UsuarioController::class, 'formData']);
        Route::post('/', [UsuarioController::class, 'store']);
        Route::put('/{usuario}', [UsuarioController::class, 'update']);
        Route::patch('/{usuario}/estado', [UsuarioController::class, 'toggleEstado']);
    });

Route::prefix('perfil')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [PerfilController::class, 'show']);
        Route::put('/', [PerfilController::class, 'update']);
        Route::put('/password', [PerfilController::class, 'changePassword']);
    });

Route::prefix('dashboard')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'show']);
    });

Route::prefix('cursos')
    ->middleware(['auth:sanctum', 'admin.role'])
    ->group(function () {
        Route::get('/', [CursoController::class, 'index']);
        Route::post('/', [CursoController::class, 'store']);
        Route::put('/{curso}', [CursoController::class, 'update']);
        Route::delete('/{curso}', [CursoController::class, 'destroy']);
    });

Route::prefix('inscripciones')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [InscripcionController::class, 'index']);
        Route::post('/', [InscripcionController::class, 'store']);
    });

Route::prefix('visualizacion-cursos')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [VisualizacionCursoController::class, 'index']);
    });

Route::prefix('docente')
    ->middleware(['auth:sanctum', 'teacher.role'])
    ->group(function () {
        Route::get('/inscripciones', [DocenteInscripcionController::class, 'index']);
        Route::patch('/inscripciones/{inscripcion}/retirar', [DocenteInscripcionController::class, 'withdraw']);
    });
