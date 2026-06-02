<?php

namespace App\Services;

use App\Repositories\EstudianteRepository;
use Illuminate\Support\Facades\Hash;

class EstudianteService
{
    // Inyección de dependencias del Repositorio (PHP 8.2+ promoted property)
    public function __construct(
        protected readonly EstudianteRepository $estudianteRepo,
        protected readonly \App\Repositories\MateriaAprobadaRepository $materiaAprobadaRepository
    ) {}

    /**
     * Lógica para obtener el perfil formateado
     */
    public function obtenerPerfilCompleto(int $idUsuario)
    {
        return $this->estudianteRepo->findUsuarioConRol($idUsuario);
    }

    /**
     * Lógica para actualizar datos comunes
     */
    public function actualizarDatos(int $idUsuario, array $data)
    {
        return $this->estudianteRepo->updateUsuario($idUsuario, $data);
    }

    /**
     * Lógica de seguridad para el cambio de contraseña
     */
    public function modificarContrasena($user, array $data): array
    {
        // Revisamos si la contraseña vieja coincide con la encriptada en la BD
        if (!Hash::check($data['password_actual'], $user->Contrasena)) {
            return [
                'status' => false,
                'message' => 'La contraseña actual es incorrecta.',
                'data' => []
            ];
        }

        // Si es correcta, encriptamos la nueva 'Contrasena' y mandamos a actualizar
        $this->estudianteRepo->updateUsuario($user->IdUsuario, [
            'Contrasena' => Hash::make($data['Contrasena'])
        ]);

        return [
            'status' => true,
            'message' => 'Contraseña cambiada con éxito.',
            'data' => []
        ];
    }

    /**
     * RF05 – Lógica para listar materias según la modalidad del estudiante
     */
    public function listarMateriasDisponibles(int $idUsuario): array
    {
        // 1. Conseguimos el registro académico del estudiante para sacar su modalidad
        $estudianteCarrera = $this->estudianteRepo->getModalidadEstudiante($idUsuario);

        if (!$estudianteCarrera) {
            return [
                'materias' => [],
                'materias_aprobadas' => 0,
            ];
        }

        // 2. Pasamos el IdModalidad correcto al repositorio
        $materias = $this->estudianteRepo->getMateriasPorModalidad($idUsuario, $estudianteCarrera->IdModalidad);

        return [
            'materias' => $materias,
            'materias_aprobadas' => $this->materiaAprobadaRepository->getApprovedCount($idUsuario),
        ];
    }

    /**
     * RF05 – Lógica de inscripción controlando duplicidad por materia y turno
     */
    public function procesarInscripcionMateria(int $idUsuario, int $idCursoMateria): array
    {
        $materiaObjetivo = $this->estudianteRepo->findCursoMateriaConDetalles($idCursoMateria);
        
        if (!$materiaObjetivo) {
            return ['status' => false, 'message' => 'El curso-materia seleccionado no existe.', 'data' => []];
        }

        // REGLA DE NEGOCIO: Evitar duplicidad o cruce de horarios
        $yaInscrito = $this->estudianteRepo->verificarCruceODuplicado(
            $idUsuario, 
            $materiaObjetivo->IdMateria, 
            $materiaObjetivo->IdTurno
        );

        if ($yaInscrito) {
            return [
                'status' => false,
                'message' => 'Inscripción rechazada: Ya estás inscrito en esta materia o tienes otra materia en el mismo turno.',
                'data' => []
            ];
        }

        $exito = $this->estudianteRepo->registrarInscripcionMateria($idUsuario, $idCursoMateria);

        return [
            'status' => $exito,
            'message' => $exito ? 'Inscripción a la materia realizada con éxito.' : 'No se pudo completar la inscripción.',
            'data' => []
        ];
    }
}