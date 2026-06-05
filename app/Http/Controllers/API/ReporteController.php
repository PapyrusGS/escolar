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
        $user = $request->user();

        $params = $request->only(['IdCarrera']);
        $params['IdUsuario'] = $user->IdUsuario;

        try {
            $response = $this->reporteService->generarReporte($user->IdRol, 'materias_carrera', $params);
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
        $allParams = $request->all();
        $tipoReporte = $allParams['tipoReporte'] ?? '';
        unset($allParams['tipoReporte']);
        $allParams['IdUsuario'] = $user->IdUsuario;

        try {
            $response = $this->reporteService->generarReporte($user->IdRol, $tipoReporte, $allParams);
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    public function filtros(Request $request): JsonResponse
    {
        $user = $request->user();
        $tipoReporte = $request->input('tipoReporte', '');

        try {
            $filtros = $this->reporteService->obtenerFiltrosPorRol($user->IdRol, $tipoReporte);
            return response()->json([
                'status' => true,
                'data' => $filtros
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }

    public function tiposReporte(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            $tipos = $this->reporteService->obtenerTiposReportesPorRol($user->IdRol);
            return response()->json([
                'status' => true,
                'data' => $tipos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }

    public function filterData(Request $request, string $tipo): JsonResponse
    {
        try {
            $data = match ($tipo) {
                'carreras' => \DB::table('carreras')->where('Estado', true)->get(['IdCarrera', 'Nombre']),
                'cursos' => \DB::table('cursos')->where('Estado', true)->get(['IdCurso', 'Aula']),
                'materias' => \DB::table('materias')
                    ->leftJoin('carreraMateriaPensum', 'materias.IdMateria', '=', 'carreraMateriaPensum.IdMateria')
                    ->leftJoin('carreras', 'carreraMateriaPensum.IdCarrera', '=', 'carreras.IdCarrera')
                    ->where('materias.Estado', true)
                    ->select('materias.IdMateria', 'materias.CodigoMateria', 'materias.Nombre', 'carreras.Nombre as Carrera')
                    ->distinct()
                    ->get(),
                'docentes' => \DB::table('usuarios')->where('IdRol', 2)->where('Estado', true)->get(['IdUsuario', 'Nombre1', 'Apellido1']),
                'estudiantes' => \DB::table('usuarios')->where('IdRol', 3)->where('Estado', true)->get(['IdUsuario', 'Nombre1', 'Apellido1', 'CI']),
                'cursos_por_docente' => $this->getCursosPorDocente($request),
                'estudiantes_por_docente' => $this->getEstudiantesPorDocente($request),
                default => [],
            };

            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }

    private function getCursosPorDocente(Request $request): array
    {
        $user = $request->user();
        return \DB::table('cursos_materias')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->where('cursos_materias.IdDocente', $user->IdUsuario)
            ->where('cursos.Estado', true)
            ->select('cursos.IdCurso', 'cursos.Aula')
            ->distinct()
            ->get()
            ->toArray();
    }

    private function getEstudiantesPorDocente(Request $request): array
    {
        $user = $request->user();
        return \DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('usuarios', 'EstudianteCarrera.IdUsuario', '=', 'usuarios.IdUsuario')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->where('cursos_materias.IdDocente', $user->IdUsuario)
            ->where('usuarios.Estado', true)
            ->select('usuarios.IdUsuario', 'usuarios.Nombre1', 'usuarios.Apellido1', 'usuarios.CI')
            ->distinct()
            ->get()
            ->toArray();
    }
}
