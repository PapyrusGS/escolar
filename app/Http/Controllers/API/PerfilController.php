<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Perfil\ChangePasswordRequest;
use App\Http\Requests\Perfil\UpdatePerfilRequest;
use App\Services\PerfilService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PerfilController extends Controller
{
    public function __construct(
        private readonly PerfilService $perfilService
    ) {
    }

    public function show(Request $request): JsonResponse
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

            return response()->json($this->perfilService->show($usuario), 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al obtener el perfil.',
                'data' => [],
            ], 500);
        }
    }

    public function update(UpdatePerfilRequest $request): JsonResponse
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

            return response()->json($this->perfilService->update($usuario, $request->validated()), 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al actualizar el perfil.',
                'data' => [],
            ], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
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
                $this->perfilService->changePassword(
                    $usuario,
                    $request->validated('current_password'),
                    $request->validated('password')
                ),
                200
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al cambiar la contraseña.',
                'data' => [],
            ], 500);
        }
    }
}
