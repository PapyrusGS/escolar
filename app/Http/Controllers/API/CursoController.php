<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curso\StoreCursoRequest;
use App\Http\Requests\Curso\UpdateCursoRequest;
use App\Models\Curso;
use App\Services\CursoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CursoController extends Controller
{
    public function __construct(
        private readonly CursoService $cursoService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            return response()->json(
                $this->cursoService->index(
                    $request->only(['search', 'Estado']),
                    (int) $request->input('per_page', 10)
                ),
                200
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al cargar el listado de cursos.',
                'data' => [],
            ], 500);
        }
    }

    public function store(StoreCursoRequest $request): JsonResponse
    {
        try {
            return response()->json(
                $this->cursoService->store($request->validated()),
                201
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al crear el curso.',
                'data' => [],
            ], 500);
        }
    }

    public function update(UpdateCursoRequest $request, Curso $curso): JsonResponse
    {
        try {
            return response()->json(
                $this->cursoService->update($curso, $request->validated()),
                200
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al actualizar el curso.',
                'data' => [],
            ], 500);
        }
    }

    public function destroy(Curso $curso): JsonResponse
    {
        try {
            $result = $this->cursoService->delete($curso);

            return response()->json($result, $result['status'] ? 200 : 409);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al eliminar el curso.',
                'data' => [],
            ], 500);
        }
    }
}
