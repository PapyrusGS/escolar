<?php

namespace App\Repositories;

use App\Models\Curso;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CursoRepository
{
    public function paginateWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Curso::query();

        $estado = $filters['Estado'] ?? null;
        if ($estado !== null && $estado !== '') {
            $query->where('Estado', filter_var($estado, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $search = trim((string) $search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('Nombre', 'like', "%{$search}%")
                    ->orWhere('Descripcion', 'like', "%{$search}%")
                    ->orWhere('IdCurso', 'like', "%{$search}%");
            });
        });

        return $query->orderByDesc('IdCurso')->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Curso
    {
        return Curso::query()->create($data);
    }

    public function findById(int $id): ?Curso
    {
        return Curso::query()->find($id);
    }

    public function update(Curso $curso, array $data): Curso
    {
        $curso->fill($data);
        $curso->save();

        return $curso->refresh();
    }

    public function delete(Curso $curso): void
    {
        $curso->delete();
    }

    public function hasDependencies(Curso $curso): bool
    {
        return \DB::table('cursos_materias')
            ->where('IdCurso', $curso->IdCurso)
            ->exists();
    }
}
