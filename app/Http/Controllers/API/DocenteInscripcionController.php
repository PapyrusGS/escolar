<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Services\InscripcionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DocenteInscripcionController extends Controller
{
    public function __construct(
        private readonly InscripcionService $inscripcionService
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

            $result = $this->inscripcionService->teacherDashboard(
                $usuario,
                $request->only(['search']),
                $request->integer('IdCursoMateria'),
                (int) $request->input('per_page', 10)
            );

            return response()->json($result, $result['status'] ? 200 : 403);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrio un error al cargar el panel del docente.',
                'data' => [],
            ], 500);
        }
    }

    public function withdraw(Request $request, Inscripcion $inscripcion): JsonResponse
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

            $result = $this->inscripcionService->withdrawStudent($usuario, $inscripcion);

            return response()->json($result, $result['status'] ? 200 : 409);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrio un error al retirar al estudiante.',
                'data' => [],
            ], 500);
        }
    }
}
