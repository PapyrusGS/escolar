<?php

namespace App\Repositories;

use App\Models\CursoMateria;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CursoVisualizacionRepository
{
    public function paginateForUser(User $user, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        if ((int) $user->IdRol === 3) {
            return $this->paginateStudentCourses($user, $filters, $perPage);
        }

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
            ->where('Estado', true);

        if ((int) $user->IdRol === 2) {
            $query->where('IdDocente', $user->IdUsuario);
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

    public function paginateStudentCourses(User $user, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Inscripcion::query()
            ->with([
                'cursoMateria.curso',
                'cursoMateria.materia',
                'cursoMateria.docente.rol',
                'cursoMateria.turno',
            ])
            ->where('IdEstudiante', $user->IdUsuario)
            ->where('Estado', true);

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $search = trim((string) $search);

            $query->whereHas('cursoMateria', function ($subQuery) use ($search) {
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
            $query->whereHas('cursoMateria', function ($subQuery) use ($idCurso) {
                $subQuery->where('IdCurso', $idCurso);
            });
        });

        $query->when($filters['IdTurno'] ?? null, function ($query, $idTurno) {
            $query->whereHas('cursoMateria', function ($subQuery) use ($idTurno) {
                $subQuery->where('IdTurno', $idTurno);
            });
        });

        return $query->orderByDesc('IdInscripcion')->paginate($perPage)->withQueryString();
    }

    public function findForUser(User $user, int $id): CursoMateria|Inscripcion|null
    {
        if ((int) $user->IdRol === 3) {
            return Inscripcion::query()
                ->with([
                    'cursoMateria.curso',
                    'cursoMateria.materia',
                    'cursoMateria.docente.rol',
                    'cursoMateria.turno',
                ])
                ->where('IdInscripcion', $id)
                ->where('IdEstudiante', $user->IdUsuario)
                ->where('Estado', true)
                ->first();
        }

        $query = CursoMateria::query()
            ->with([
                'curso',
                'materia',
                'docente.rol',
                'turno',
                'inscripciones.estudiante.rol',
            ])
            ->withCount([
                'inscripciones as inscritos_activos_count' => function ($query) {
                    $query->where('Estado', true);
                },
            ])
            ->where('IdCursoMateria', $id)
            ->where('Estado', true);

        if ((int) $user->IdRol === 2) {
            $query->where('IdDocente', $user->IdUsuario);
        }

        return $query->first();
    }
}
