<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inscripcion\StoreInscripcionRequest;
use App\Services\InscripcionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class InscripcionController extends Controller
{
    public function __construct(
        private readonly InscripcionService $inscripcionService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $usuario = $request->user();

            if (! $usuario || (int) $usuario->IdRol !== 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Solo los estudiantes pueden consultar las inscripciones.',
                    'data' => [],
                ], 403);
            }

            return response()->json(
                $this->inscripcionService->index(
                    $usuario,
                    $request->only(['search', 'IdCurso', 'IdTurno']),
                    (int) $request->input('per_page', 10)
                ),
                200
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrio un error al cargar el catalogo de inscripciones.',
                'data' => [],
            ], 500);
        }
    }

    public function store(StoreInscripcionRequest $request): JsonResponse
    {
        try {
            $usuario = $request->user();

            if (! $usuario || (int) $usuario->IdRol !== 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Solo los estudiantes pueden inscribirse.',
                    'data' => [],
                ], 403);
            }

            $result = $this->inscripcionService->store($usuario, $request->validated());

            return response()->json($result, $result['status'] ? 201 : 409);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrio un error al registrar la inscripcion.',
                'data' => [],
            ], 500);
        }
    }
}
