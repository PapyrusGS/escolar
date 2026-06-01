<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        DB::table('usuarios')->insert($data);

        return User::query()
            ->with('rol')
            ->where('Correo', $data['Correo'])
            ->firstOrFail();
    }

    public function findById(int $id): ?User
    {
        return User::query()
            ->with('rol')
            ->where('IdUsuario', $id)
            ->first();
    }

    public function all(): iterable
    {
        return User::query()
            ->with('rol')
            ->leftJoin('EstudianteCarrera', 'usuarios.IdUsuario', '=', 'EstudianteCarrera.IdUsuario')
            ->select(
                'usuarios.*',
                'EstudianteCarrera.IdCarrera',
                'EstudianteCarrera.IdModalidad'
            )
            ->get();
    }

    public function update(int $id, array $data): bool
    {
        return DB::table('usuarios')
            ->where('IdUsuario', $id)
            ->update($data);
    }

    public function delete(int $id): bool
    {
        return DB::table('usuarios')
            ->where('IdUsuario', $id)
            ->delete();
    }
}
