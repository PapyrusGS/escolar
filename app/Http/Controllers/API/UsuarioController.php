<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Models\User;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $result = $this->usuarioService->formData();

            return response()->json($result, 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al cargar el formulario.',
                'data' => [],
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->usuarioService->index(
                $request->only(['search', 'IdRol', 'Estado']),
                (int) $request->input('per_page', 10)
            );

            return response()->json($result, 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al cargar el listado de usuarios.',
                'data' => [],
            ], 500);
        }
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        try {
            $result = $this->usuarioService->store($request->validated());

            return response()->json($result, 201);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al registrar el usuario.',
                'data' => [],
            ], 500);
        }
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): JsonResponse
    {
        try {
            $result = $this->usuarioService->update($usuario, $request->validated());

            return response()->json($result, 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al actualizar el usuario.',
                'data' => [],
            ], 500);
        }
    }

    public function toggleEstado(User $usuario): JsonResponse
    {
        try {
            $result = $this->usuarioService->toggleEstado($usuario);

            return response()->json($result, 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al cambiar el estado del usuario.',
                'data' => [],
            ], 500);
        }
    }
}
