<?php

namespace App\Repositories;

use App\Models\CursoMateria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CursoMateriaRepository
{
    public function paginateAvailableForStudent(int $studentId, ?int $careerId, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = CursoMateria::query()
            ->with([
                'curso',
                'materia',
                'docente.rol',
                'turno',
            ])
            ->withCount([
                'inscripciones as inscritos_activos_count' => function ($query) {
                    $query->where('Estado', true);
                },
            ])
            ->where('Estado', true)
            ->whereDoesntHave('inscripciones', function ($query) use ($studentId) {
                $query->where('IdEstudiante', $studentId)
                    ->where('Estado', true);
            })
            ->whereRaw('(
                SELECT COUNT(*)
                FROM inscripciones
                WHERE inscripciones.IdCursoMateria = cursos_materias.IdCursoMateria
                  AND inscripciones.Estado = 1
            ) < cursos_materias.MaxInscritos');

        if ($careerId) {
            $query->whereHas('materia', function ($query) use ($careerId) {
                $query->where('IdCarrera', $careerId);
            });
        }

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $search = trim((string) $search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('curso', function ($query) use ($search) {
                    $query->where('Nombre', 'like', "%{$search}%");
                })->orWhereHas('materia', function ($query) use ($search) {
                    $query->where('Nombre', 'like', "%{$search}%");
                })->orWhereHas('docente', function ($query) use ($search) {
                    $query->where('Nombre1', 'like', "%{$search}%")
                        ->orWhere('Nombre2', 'like', "%{$search}%")
                        ->orWhere('Apellido1', 'like', "%{$search}%")
                        ->orWhere('Apellido2', 'like', "%{$search}%");
                })->orWhereHas('turno', function ($query) use ($search) {
                    $query->where('Nombre', 'like', "%{$search}%")
                        ->orWhere('Dias', 'like', "%{$search}%");
                });
            });
        });

        $query->when($filters['IdCurso'] ?? null, function ($query, $idCurso) {
            $query->where('IdCurso', $idCurso);
        });

        $query->when($filters['IdTurno'] ?? null, function ($query, $idTurno) {
            $query->where('IdTurno', $idTurno);
        });

        return $query->orderByDesc('IdCursoMateria')->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?CursoMateria
    {
        return CursoMateria::query()
            ->with([
                'curso',
                'materia',
                'docente.rol',
                'turno',
            ])
            ->find($id);
    }

    public function teacherAssignments(int $docenteId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = CursoMateria::query()
            ->with([
                'curso',
                'materia',
                'turno',
            ])
            ->withCount([
                'inscripciones as inscritos_activos_count' => function ($query) {
                    $query->where('Estado', true);
                },
            ])
            ->where('IdDocente', $docenteId)
            ->where('Estado', true);

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $search = trim((string) $search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('curso', function ($query) use ($search) {
                    $query->where('Nombre', 'like', "%{$search}%");
                })->orWhereHas('materia', function ($query) use ($search) {
                    $query->where('Nombre', 'like', "%{$search}%");
                })->orWhereHas('turno', function ($query) use ($search) {
                    $query->where('Nombre', 'like', "%{$search}%")
                        ->orWhere('Dias', 'like', "%{$search}%");
                });
            });
        });

        return $query->orderByDesc('IdCursoMateria')->paginate($perPage)->withQueryString();
    }

    public function teacherAssignmentsList(int $docenteId): Collection
    {
        return CursoMateria::query()
            ->with(['curso', 'materia', 'turno'])
            ->withCount([
                'inscripciones as inscritos_activos_count' => function ($query) {
                    $query->where('Estado', true);
                },
            ])
            ->where('IdDocente', $docenteId)
            ->where('Estado', true)
            ->orderByDesc('IdCursoMateria')
            ->get();
    }
}
