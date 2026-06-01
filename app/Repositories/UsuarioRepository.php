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
}
