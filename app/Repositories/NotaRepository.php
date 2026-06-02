<?php

namespace App\Repositories;

use App\Models\Nota;
use Illuminate\Support\Collection;

class NotaRepository
{
    public function findByInscripcion(int $idInscripcion): ?Nota
    {
        return Nota::query()
            ->where('IdInscripcion', $idInscripcion)
            ->first();
    }

    public function find(int $idNota): ?Nota
    {
        return Nota::query()
            ->where('IdNota', $idNota)
            ->first();
    }

    public function create(array $data): Nota
    {
        $id = \Illuminate\Support\Facades\DB::table('notas')->insertGetId($data);

        return Nota::query()->where('IdNota', $id)->first();
    }

    public function update(int $idNota, array $data): bool
    {
        return (bool) \Illuminate\Support\Facades\DB::table('notas')
            ->where('IdNota', $idNota)
            ->update($data);
    }

    public function getByCursoMateria(int $idCursoMateria): Collection
    {
        return \Illuminate\Support\Facades\DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('usuarios', 'EstudianteCarrera.IdUsuario', '=', 'usuarios.IdUsuario')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->where('inscripciones.IdCursoMateria', $idCursoMateria)
            ->where('usuarios.Estado', true)
            ->select(
                'inscripciones.IdInscripcion',
                'usuarios.IdUsuario',
                \Illuminate\Support\Facades\DB::raw("CONCAT(usuarios.Nombre1, ' ', COALESCE(usuarios.Nombre2, ''), ' ', usuarios.Apellido1, ' ', COALESCE(usuarios.Apellido2, '')) as Estudiante"),
                'usuarios.CI',
                'notas.Nota',
                'notas.IdNota',
                'inscripciones.Aprobado'
            )
            ->orderBy('usuarios.Apellido1')
            ->get();
    }

    public function getByIdDocente(int $idDocente): array
    {
        return \Illuminate\Support\Facades\DB::table('cursos_materias')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->where('cursos_materias.IdDocente', $idDocente)
            ->where('cursos_materias.Estado', true)
            ->select(
                'cursos_materias.IdCursoMateria',
                'materias.Nombre as MateriaNombre',
                'materias.CodigoMateria',
                'cursos.Aula',
                'cursos.Nombre as AulaNombre',
                'cursos_materias.Inscritos',
                'cursos_materias.MaxInscritos'
            )
            ->orderBy('materias.Nombre')
            ->get()
            ->toArray();
    }
}
