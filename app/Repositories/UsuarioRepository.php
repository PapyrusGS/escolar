<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UsuarioRepository
{
    public function findActiveByCorreo(string $correo): ?User
    {
        return User::query()
            ->with('rol')
            ->where('Correo', $correo)
            ->where('Estado', true)
            ->first();
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function paginateWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query()->with('rol');

        $query->when($filters['IdRol'] ?? null, function ($query, $idRol) {
            $query->where('IdRol', $idRol);
        });

        $estado = $filters['Estado'] ?? null;
        if ($estado !== null && $estado !== '') {
            $query->where('Estado', filter_var($estado, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $search = trim((string) $search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('Nombre1', 'like', "%{$search}%")
                    ->orWhere('Nombre2', 'like', "%{$search}%")
                    ->orWhere('Apellido1', 'like', "%{$search}%")
                    ->orWhere('Apellido2', 'like', "%{$search}%")
                    ->orWhere('Correo', 'like', "%{$search}%")
                    ->orWhere('CI', 'like', "%{$search}%");
            });
        });

        return $query->orderByDesc('IdUsuario')->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?User
    {
        return User::query()->with('rol')->find($id);
    }

    public function update(User $usuario, array $data): User
    {
        $usuario->fill($data);
        $usuario->save();

        return $usuario->refresh()->load('rol');
    }

    public function updateSelf(User $usuario, array $data): User
    {
        return $this->update($usuario, $data);
    }

    public function toggleEstado(User $usuario): User
    {
        $usuario->Estado = ! (bool) $usuario->Estado;
        $usuario->save();

        return $usuario->refresh()->load('rol');
    }
}
