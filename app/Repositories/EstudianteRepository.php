<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class EstudianteRepository
{
    /**
     * Busca los datos del estudiante cruzando con su rol
     */
    public function findUsuarioConRol(int $idUsuario)
    {
        return DB::table('usuarios')
            ->join('roles', 'usuarios.IdRol', '=', 'roles.IdRol')
            ->where('usuarios.IdUsuario', $idUsuario)
            ->select(
                'usuarios.IdUsuario',
                'usuarios.Nombre1',
                'usuarios.Nombre2',
                'usuarios.Apellido1',
                'usuarios.Apellido2',
                'usuarios.Correo',
                'usuarios.CI',
                'usuarios.Telefono',
                'roles.Nombre as Rol'
            )
            ->first();
    }

    /**
     * Actualiza cualquier campo en la tabla usuarios de forma dinámica
     */
    public function updateUsuario(int $idUsuario, array $data)
    {
        // Hace el UPDATE directo en la base de datos
        DB::table('usuarios')
            ->where('IdUsuario', $idUsuario)
            ->update($data);
        
        // Retorna los datos limpios y actuales directo de la BD para confirmar
        return DB::table('usuarios')
            ->where('IdUsuario', $idUsuario)
            ->select('IdUsuario', 'Nombre1', 'Nombre2', 'Apellido1', 'Apellido2', 'Correo', 'Telefono', 'CI')
            ->first();
    }
}