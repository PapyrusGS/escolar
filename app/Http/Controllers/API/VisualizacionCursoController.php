<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\VisualizacionCursoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class VisualizacionCursoController extends Controller
{
    public function __construct(
        private readonly VisualizacionCursoService $visualizacionCursoService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $usuario = $request->user();

            if (! $usuario) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autenticado.',
                    'data' => [],
                ], 401);
            }

            return response()->json(
                $this->visualizacionCursoService->index(
                    $usuario,
                    $request->only(['search', 'IdCurso', 'IdTurno']),
                    (int) $request->input('per_page', 8),
                    $request->integer('selected')
                ),
                200
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrio un error al cargar la visualizacion de cursos.',
                'data' => [],
            ], 500);
        }
    }
}
