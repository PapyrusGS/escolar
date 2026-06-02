<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nota\StoreNotaRequest;
use App\Http\Requests\Nota\UpdateNotaRequest;
use App\Services\NotaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class NotaController extends Controller
{
    public function __construct(
        private readonly NotaService $notaService
    ) {
    }

    public function index(Request $request, int $idCursoMateria): JsonResponse
    {
        try {
            $notas = $this->notaService->listarNotasPorCurso(
                (int) $request->user()->IdUsuario,
                $idCursoMateria
            );

            return response()->json([
                'status' => true,
                'message' => 'Notas del curso obtenidas correctamente.',
                'data' => $notas,
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(StoreNotaRequest $request): JsonResponse
    {
        try {
            $result = $this->notaService->asignarNota(
                (int) $request->user()->IdUsuario,
                (int) $request->validated('IdInscripcion'),
                (float) $request->validated('Nota')
            );

            return response()->json($result, 201);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }
    }

    public function update(UpdateNotaRequest $request, int $idNota): JsonResponse
    {
        try {
            $result = $this->notaService->editarNota(
                (int) $request->user()->IdUsuario,
                $idNota,
                (float) $request->validated('Nota')
            );

            return response()->json($result, 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }
    }

    public function rendimiento(Request $request, int $idCursoMateria): JsonResponse
    {
        try {
            $data = $this->notaService->visualizarRendimiento(
                (int) $request->user()->IdUsuario,
                $idCursoMateria
            );

            return response()->json([
                'status' => true,
                'message' => 'Rendimiento del curso obtenido correctamente.',
                'data' => $data,
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function cursos(Request $request): JsonResponse
    {
        try {
            $cursos = $this->notaService->obtenerCursosDelDocente(
                (int) $request->user()->IdUsuario
            );

            return response()->json([
                'status' => true,
                'data' => $cursos,
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
