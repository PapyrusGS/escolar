<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ReporteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct(
        protected readonly ReporteService $reporteService
    ) {}

    public function materiasPorCarrera(Request $request): JsonResponse
    {
        // El reporte de materias por carrera se puede acceder por Administrador (Rol 1)
        // Pero usamos el patrón Strategy pasándole su Rol.
        $user = $request->user();
        
        $params = $request->only(['IdCarrera']);
        $params['IdUsuario'] = $user->IdUsuario;

        try {
            $response = $this->reporteService->generarReporte($user->IdRol, $params);
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    public function reportePorRol(Request $request): JsonResponse
    {
        $user = $request->user();
        $params = $request->all();
        $params['IdUsuario'] = $user->IdUsuario;

        try {
            $response = $this->reporteService->generarReporte($user->IdRol, $params);
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }
}
