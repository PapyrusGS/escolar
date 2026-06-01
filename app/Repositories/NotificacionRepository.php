<?php

namespace App\Repositories;

use App\Models\Notificacion;
use Illuminate\Support\Collection;

class NotificacionRepository
{
    public function getByUsuario(int $idUsuario): Collection
    {
        return Notificacion::where('IdUsuario', $idUsuario)
            ->orderBy('FechaEnvio', 'desc')
            ->get();
    }

    public function findById(int $idNotificacion): ?Notificacion
    {
        return Notificacion::find($idNotificacion);
    }

    public function create(array $data): Notificacion
    {
        return Notificacion::create($data);
    }

    public function update(Notificacion $notificacion, array $data): bool
    {
        return $notificacion->update($data);
    }

    public function delete(Notificacion $notificacion): bool
    {
        return $notificacion->delete();
    }
}
