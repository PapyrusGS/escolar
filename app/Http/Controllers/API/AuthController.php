<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->validated('Correo'),
                $request->validated('Contrasena')
            );

            return response()->json($result, $result['status'] ? 200 : 401);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al iniciar sesión.',
                'data' => [],
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
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

            $result = $this->authService->logout($usuario);

            return response()->json($result, 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al cerrar sesión.',
                'data' => [],
            ], 500);
        }
    }

    public function profile(Request $request): JsonResponse
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

            $result = $this->authService->profile($usuario);

            return response()->json($result, 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al obtener el perfil.',
                'data' => [],
            ], 500);
        }
    }
}
