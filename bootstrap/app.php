<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route; // <-- Agregado únicamente esto para las rutas

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // --- Agregado únicamente este bloque para tus rutas de estudiante ---
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/estudiantes_api.php'));
        },
        // -------------------------------------------------------------------
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.role' => \App\Http\Middleware\EnsureAdminRole::class,
            'teacher.role' => \App\Http\Middleware\EnsureTeacherRole::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('welcome'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, Request $request) {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => false,
                'message' => 'Datos de entrada no válidos.',
                'data' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return redirect('/');
            }

            return response()->json([
                'status' => false,
                'message' => $e->getMessage() ?: 'No autenticado.',
                'data' => [],
            ], 401);
        });
    })->create();