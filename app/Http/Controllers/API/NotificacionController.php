<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\NotificacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function __construct(
        protected readonly NotificacionService $notificacionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $idUsuario = $request->user()->IdUsuario;
        $response = $this->notificacionService->obtenerNotificacionesUsuario($idUsuario);
        return response()->json($response, $response['status'] ? 200 : 400);
    }

    public function toggle(int $id, Request $request): JsonResponse
    {
        $idUsuario = $request->user()->IdUsuario;
        $response = $this->notificacionService->alternarEstado($id, $idUsuario);
        return response()->json($response, $response['status'] ? 200 : 404);
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        $idUsuario = $request->user()->IdUsuario;
        $response = $this->notificacionService->eliminarNotificacion($id, $idUsuario);
        return response()->json($response, $response['status'] ? 200 : 404);
    }

    public function store(Request $request): JsonResponse
    {
        // Ruta para que el admin pueda emitir notificaciones
        if ($request->user()->IdRol !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'IdUsuario' => 'required|exists:usuarios,IdUsuario',
            'Titulo' => 'required|string|max:100',
            'Contenido' => 'required|string',
        ]);

        $response = $this->notificacionService->crearNotificacion($validated);
        return response()->json($response, 201);
    }
}
