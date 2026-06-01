<?php

namespace App\Services;

use App\Repositories\NotificacionRepository;
use App\Models\Notificacion;
use Illuminate\Support\Collection;

class NotificacionService
{
    public function __construct(
        protected readonly NotificacionRepository $notificacionRepository
    ) {}

    public function obtenerNotificacionesUsuario(int $idUsuario): array
    {
        $notificaciones = $this->notificacionRepository->getByUsuario($idUsuario);
        return [
            'status' => true,
            'message' => 'Notificaciones recuperadas con éxito',
            'data' => $notificaciones
        ];
    }

    public function alternarEstado(int $idNotificacion, int $idUsuario): array
    {
        $notificacion = $this->notificacionRepository->findById($idNotificacion);

        if (!$notificacion || $notificacion->IdUsuario !== $idUsuario) {
            return [
                'status' => false,
                'message' => 'Notificación no encontrada o no pertenece al usuario',
                'data' => null
            ];
        }

        $this->notificacionRepository->update($notificacion, [
            'Estado' => !$notificacion->Estado
        ]);

        return [
            'status' => true,
            'message' => 'Estado de la notificación actualizado correctamente',
            'data' => $notificacion->fresh()
        ];
    }

    public function eliminarNotificacion(int $idNotificacion, int $idUsuario): array
    {
        $notificacion = $this->notificacionRepository->findById($idNotificacion);

        if (!$notificacion || $notificacion->IdUsuario !== $idUsuario) {
            return [
                'status' => false,
                'message' => 'Notificación no encontrada o no pertenece al usuario',
                'data' => null
            ];
        }

        $this->notificacionRepository->delete($notificacion);

        return [
            'status' => true,
            'message' => 'Notificación eliminada correctamente',
            'data' => null
        ];
    }

    public function crearNotificacion(array $data): array
    {
        $data['FechaEnvio'] = $data['FechaEnvio'] ?? now();
        $data['FechaRegistro'] = $data['FechaRegistro'] ?? now();
        $data['Estado'] = $data['Estado'] ?? true;

        $notificacion = $this->notificacionRepository->create($data);

        return [
            'status' => true,
            'message' => 'Notificación creada con éxito',
            'data' => $notificacion
        ];
    }
}
