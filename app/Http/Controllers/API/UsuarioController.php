<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Throwable;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarioService
    ) {
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
}
