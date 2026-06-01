<?php

use App\Http\Controllers\API\EstudianteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('estudiante')->group(function () {
    
    // RF03 - Gestión de perfiles (Ya los tienes)
    Route::get('/perfil', [EstudianteController::class, 'verPerfil']);
    Route::put('/perfil/editar', [EstudianteController::class, 'editarPerfil']);
    Route::put('/perfil/contrasena', [EstudianteController::class, 'cambiarContrasena']);
    
    // RF05 - Inscripción a cursos
    Route::get('/cursos/disponibles', [EstudianteController::class, 'cursosDisponibles']);
    Route::post('/cursos/inscribir', [EstudianteController::class, 'inscribirCurso']);
    
});