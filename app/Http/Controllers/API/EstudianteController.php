<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\EstudianteService;
use App\Http\Requests\Estudiante\EditarPerfilRequest;
use App\Http\Requests\Estudiante\CambiarContrasenaRequest; // Importamos el de contraseña también
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class EstudianteController extends Controller
{
    public function __construct(
        protected readonly EstudianteService $estudianteService
    ) {}

    /**
     * RF03 – Visualizar su información (¡LA QUE FALTABA!)
     */
    public function verPerfil(Request $request): JsonResponse
    {
        try {
            // Buscamos el perfil usando el IdUsuario que viene encriptado en el Token
            $perfil = $this->estudianteService->obtenerPerfilCompleto($request->user()->IdUsuario);

            return response()->json([
                'status' => true,
                'message' => 'Perfil del estudiante obtenido con éxito.',
                'data' => $perfil
            ], 200);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al obtener el perfil.',
                'data' => []
            ], 500);
        }
    }

    /**
     * RF03 – Editar datos personales
     */
    public function editarPerfil(EditarPerfilRequest $request): JsonResponse
    {
        try {
            $usuarioActualizado = $this->estudianteService->actualizarDatos(
                $request->user()->IdUsuario, 
                $request->validated()
            );

            return response()->json([
                'status' => true,
                'message' => 'Datos personales actualizados correctamente.',
                'data' => $usuarioActualizado
            ], 200);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'status' => false, 
                'message' => 'Error en el servidor.', 
                'data' => []
            ], 500);
        }
    }

    /**
     * RF03 – Cambiar contraseña
     */
    public function cambiarContrasena(CambiarContrasenaRequest $request): JsonResponse
    {
        try {
            // Pasamos el objeto del usuario logueado y los datos validados del request
            $result = $this->estudianteService->modificarContrasena(
                $request->user(), 
                $request->validated()
            );

            return response()->json(
                $result, 
                $result['status'] ? 200 : 400
            );
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'status' => false, 
                'message' => 'Error al procesar el cambio de contraseña.', 
                'data' => []
            ], 500);
        }
    }
}