<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsuarioController;
use App\Http\Controllers\API\CursoMateriaController;
use App\Http\Controllers\API\DocenteCursoController;
use App\Http\Controllers\API\NotaController;
use App\Http\Controllers\API\EstudianteController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/perfil', [AuthController::class, 'profile']);
        Route::put('/perfil', [AuthController::class, 'updateProfile']);
        Route::put('/contrasena', [AuthController::class, 'changePassword']);
        Route::get('/dashboard/stats', [\App\Http\Controllers\API\DashboardController::class, 'getStats']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // Notificaciones
    Route::get('/notificaciones', [\App\Http\Controllers\API\NotificacionController::class, 'index']);
    Route::patch('/notificaciones/{id}/toggle', [\App\Http\Controllers\API\NotificacionController::class, 'toggle']);
    Route::delete('/notificaciones/{id}', [\App\Http\Controllers\API\NotificacionController::class, 'destroy']);
    Route::post('/notificaciones', [\App\Http\Controllers\API\NotificacionController::class, 'store']);

    // Reportes
    Route::get('/reportes/materias-carrera', [\App\Http\Controllers\API\ReporteController::class, 'materiasPorCarrera']);
    Route::get('/reportes/dinamico', [\App\Http\Controllers\API\ReporteController::class, 'reportePorRol']);
    Route::get('/reportes/tipos', [\App\Http\Controllers\API\ReporteController::class, 'tiposReporte']);
    Route::get('/reportes/filtros', [\App\Http\Controllers\API\ReporteController::class, 'filtros']);
    Route::get('/reportes/filter-data/{tipo}', [\App\Http\Controllers\API\ReporteController::class, 'filterData']);
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

Route::prefix('docente')
    ->middleware(['auth:sanctum', 'teacher.role'])
    ->group(function () {
        // Cursos asignados al docente
        Route::get('/cursos', [\App\Http\Controllers\API\DocenteCursoController::class, 'index']);
        Route::get('/cursos/{idCursoMateria}/alumnos', [\App\Http\Controllers\API\DocenteCursoController::class, 'alumnos']);

        // Gestión de notas
        Route::get('/notas/cursos', [\App\Http\Controllers\API\NotaController::class, 'cursos']);
        Route::get('/cursos/{idCursoMateria}/notas', [\App\Http\Controllers\API\NotaController::class, 'index']);
        Route::post('/notas', [\App\Http\Controllers\API\NotaController::class, 'store']);
        Route::put('/notas/{idNota}', [\App\Http\Controllers\API\NotaController::class, 'update']);
        Route::get('/cursos/{idCursoMateria}/rendimiento', [\App\Http\Controllers\API\NotaController::class, 'rendimiento']);
    });
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
