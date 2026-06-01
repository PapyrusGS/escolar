<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsuarioController;
use App\Http\Controllers\API\CursoMateriaController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/perfil', [AuthController::class, 'profile']);
        Route::put('/perfil', [AuthController::class, 'updateProfile']);
        Route::put('/contrasena', [AuthController::class, 'changePassword']);
    });
});

Route::prefix('usuarios')
    ->middleware(['auth:sanctum', 'admin.role'])
    ->group(function () {
        Route::get('/', [UsuarioController::class, 'index']);
        Route::get('/form-data', [UsuarioController::class, 'formData']);
        Route::post('/', [UsuarioController::class, 'store']);
        Route::put('/{id}', [UsuarioController::class, 'update']);
        Route::delete('/{id}', [UsuarioController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [UsuarioController::class, 'toggleStatus']);
    });

Route::prefix('cursos-materias')
    ->middleware(['auth:sanctum', 'admin.role'])
    ->group(function () {
        Route::get('/', [CursoMateriaController::class, 'index']);
        Route::get('/form-data', [CursoMateriaController::class, 'formData']);
        Route::post('/', [CursoMateriaController::class, 'store']);
        Route::put('/{id}', [CursoMateriaController::class, 'update']);
        Route::delete('/{id}', [CursoMateriaController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [CursoMateriaController::class, 'toggleStatus']);
        Route::get('/usuario/{idUsuario}', [CursoMateriaController::class, 'getCursosByUsuario']);
    });
