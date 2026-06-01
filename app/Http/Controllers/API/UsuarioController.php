<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Throwable;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarioService
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->usuarioService->all(),
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
            return response()->json($this->usuarioService->formData(), 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        try {
            return response()->json($this->usuarioService->store($request->validated()), 201);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function update(UpdateUsuarioRequest $request, int $id): JsonResponse
    {
        try {
            return response()->json($this->usuarioService->update($id, $request->validated()), 200);
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
            $this->usuarioService->destroy($id);
            return response()->json([
                'status' => true,
                'message' => 'Usuario eliminado correctamente.',
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
            $this->usuarioService->toggleStatus($id);
            return response()->json([
                'status' => true,
                'message' => 'Estado del usuario actualizado.',
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
