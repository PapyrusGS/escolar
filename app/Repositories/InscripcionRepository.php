<?php

namespace App\Repositories;

use App\Models\Inscripcion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InscripcionRepository
{
    public function findActiveByStudentAndCursoMateria(int $studentId, int $cursoMateriaId): ?Inscripcion
    {
        return Inscripcion::query()
            ->where('IdEstudiante', $studentId)
            ->where('IdCursoMateria', $cursoMateriaId)
            ->where('Estado', true)
            ->first();
    }

    public function findByStudentAndCursoMateria(int $studentId, int $cursoMateriaId): ?Inscripcion
    {
        return Inscripcion::query()
            ->where('IdEstudiante', $studentId)
            ->where('IdCursoMateria', $cursoMateriaId)
            ->first();
    }

    public function create(array $data): Inscripcion
    {
        return Inscripcion::query()->create($data);
    }

    public function update(Inscripcion $inscripcion, array $data): Inscripcion
    {
        $inscripcion->fill($data);
        $inscripcion->save();

        return $inscripcion->refresh();
    }

    public function countActiveByCursoMateria(int $cursoMateriaId): int
    {
        return Inscripcion::query()
            ->where('IdCursoMateria', $cursoMateriaId)
            ->where('Estado', true)
            ->count();
    }

    public function myActiveEnrollments(int $studentId): Collection
    {
        return Inscripcion::query()
            ->with([
                'cursoMateria.curso',
                'cursoMateria.materia',
                'cursoMateria.docente.rol',
                'cursoMateria.turno',
            ])
            ->where('IdEstudiante', $studentId)
            ->where('Estado', true)
            ->orderByDesc('IdInscripcion')
            ->get();
    }

    public function studentsByCursoMateria(int $cursoMateriaId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Inscripcion::query()
            ->with([
                'estudiante.rol',
                'estudiante',
                'cursoMateria.curso',
                'cursoMateria.materia',
                'cursoMateria.turno',
            ])
            ->where('IdCursoMateria', $cursoMateriaId)
            ->where('Estado', true);

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $search = trim((string) $search);
            $query->whereHas('estudiante', function ($subQuery) use ($search) {
                $subQuery->where('Nombre1', 'like', "%{$search}%")
                    ->orWhere('Nombre2', 'like', "%{$search}%")
                    ->orWhere('Apellido1', 'like', "%{$search}%")
                    ->orWhere('Apellido2', 'like', "%{$search}%")
                    ->orWhere('CI', 'like', "%{$search}%")
                    ->orWhere('Correo', 'like', "%{$search}%");
            });
        });

        return $query->orderByDesc('IdInscripcion')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByIdWithRelations(int $id): ?Inscripcion
    {
        return Inscripcion::query()
            ->with([
                'estudiante.rol',
                'cursoMateria.curso',
                'cursoMateria.materia',
                'cursoMateria.docente.rol',
                'cursoMateria.turno',
            ])
            ->find($id);
    }
}
