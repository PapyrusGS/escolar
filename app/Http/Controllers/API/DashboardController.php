<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
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

            return response()->json($this->dashboardService->show($usuario), 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Ocurrio un error al cargar el dashboard.',
                'data' => [],
            ], 500);
        }
    }
}
