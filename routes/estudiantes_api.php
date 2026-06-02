<?php

use App\Http\Controllers\API\EstudianteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('estudiante')->group(function () {

    // Gestión de perfiles
    Route::get('/perfil', [EstudianteController::class, 'verPerfil']);
    Route::put('/perfil/editar', [EstudianteController::class, 'editarPerfil']);
    Route::put('/perfil/contrasena', [EstudianteController::class, 'cambiarContrasena']);

    // Inscripción a cursos
    Route::get('/cursos/disponibles', [EstudianteController::class, 'cursosDisponibles']);
    Route::post('/cursos/inscribir', [EstudianteController::class, 'inscribirCurso']);

    // Calificaciones del estudiante
    Route::get('/notas', [EstudianteController::class, 'misNotas']);

});
