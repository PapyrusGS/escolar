<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curso\StoreCursoMateriaRequest;
use App\Http\Requests\Curso\UpdateCursoMateriaRequest;
use App\Services\CursoMateriaService;
use Illuminate\Http\JsonResponse;
use Throwable;

class CursoMateriaController extends Controller
{
    public function __construct(
        private readonly CursoMateriaService $cursoMateriaService
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->cursoMateriaService->all(),
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

    public function formData(): JsonResponse
    {
        try {
            return response()->json($this->cursoMateriaService->formData(), 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(StoreCursoMateriaRequest $request): JsonResponse
    {
        try {
            $result = $this->cursoMateriaService->store($request->validated());

            return response()->json($result, 201);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function update(UpdateCursoMateriaRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->cursoMateriaService->update($id, $request->validated());

            return response()->json($result, 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->cursoMateriaService->destroy($id);

            return response()->json([
                'status' => true,
                'action' => $result['status'],
                'message' => $result['message'],
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $this->cursoMateriaService->toggleStatus($id);

            return response()->json([
                'status' => true,
                'message' => 'Estado del curso actualizado.',
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCursosByUsuario(int $idUsuario): JsonResponse
    {
        try {
            $result = $this->cursoMateriaService->getCursosByUsuario($idUsuario);

            return response()->json([
                'status' => true,
                'data' => $result,
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
