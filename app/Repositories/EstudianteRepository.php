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
            ->join('estudiantecarrera', 'usuarios.IdUsuario', '=', 'estudiantecarrera.IdUsuario')
            ->join('modalidad', 'estudiantecarrera.IdModalidad', '=', 'modalidad.IdModalidad')
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
                'roles.Nombre as Rol',
                'modalidad.Nombre as Modalidad'
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

    /**
     * Trae los cursos activos en los que el estudiante NO figura todavía
     */
    public function getCursosNoInscritos(int $idUsuario)
    {
        // 1. Conseguimos los IDs de los cursos donde Pedro YA está inscrito
        $cursosInscritosIds = DB::table('inscripciones')
            ->where('IdUsuario', $idUsuario)
            ->pluck('IdCurso');

        // 2. Traemos todos los cursos EXCEPTUANDO los de la lista anterior
        return DB::table('cursos')
            ->whereNotIn('IdCurso', $cursosInscritosIds)
            ->where('Estado', true) // Asumiendo columna Estado = true para cursos activos
            ->select('IdCurso', 'Nombre', 'Descripcion') 
            ->get();
    }

    /**
     * Revisa si ya existe la fila en la tabla intermedia
     */
    public function verificarInscripcionExistente(int $idUsuario, int $idCurso): bool
    {
        return DB::table('inscripciones')
            ->where('IdUsuario', $idUsuario)
            ->where('IdCurso', $idCurso)
            ->exists();
    }

    /**
     * Inserta manualmente la inscripción respetando la falta de timestamps
     */
    public function registrarInscripcion(int $idUsuario, int $idCurso): bool
    {
        return DB::table('inscripciones')->insert([
            'IdUsuario' => $idUsuario,
            'IdCurso'   => $idCurso,
            'FechaInscripcion' => now() // Si usas esta columna en tu DB, si no, bórrala
        ]);
    }
}