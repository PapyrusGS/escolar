<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    public function getStats(): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autenticado.',
                ], 401);
            }

            // Invoca la lógica optimizada por roles del DashboardService
            $stats = $this->dashboardService->getStats((int) $user->IdUsuario, (int) $user->IdRol);

            // Cumple estrictamente con el shape obligatorio: status, message y data
            return response()->json([
                'status' => true,
                'message' => 'Estadísticas del panel cargadas correctamente.',
                'data' => $stats,
            ], 200);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Error al procesar los datos del panel de control.',
                'data' => null
            ], 500);
        }
    }
}