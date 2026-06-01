<?php

namespace App\Repositories;

use App\Models\CursoMateria;

class CursoMateriaRepository
{
    public function all(): iterable
    {
        return CursoMateria::query()
            ->with(['curso', 'materia', 'docente', 'turno'])
            ->get();
    }

    public function findById(int $id): ?CursoMateria
    {
        return CursoMateria::query()
            ->with(['curso', 'materia', 'docente', 'turno'])
            ->where('IdCursoMateria', $id)
            ->first();
    }

    public function create(array $data): CursoMateria
    {
        $id = \Illuminate\Support\Facades\DB::table('cursos_materias')->insertGetId($data);

        return $this->findById($id);
    }

    public function update(int $id, array $data): bool
    {
        return \Illuminate\Support\Facades\DB::table('cursos_materias')
            ->where('IdCursoMateria', $id)
            ->update($data);
    }

    public function delete(int $id): bool
    {
        return \Illuminate\Support\Facades\DB::table('cursos_materias')
            ->where('IdCursoMateria', $id)
            ->delete();
    }
}
