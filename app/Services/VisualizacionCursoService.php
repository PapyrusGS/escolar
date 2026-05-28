<?php

namespace App\Services;

use App\Models\CursoMateria;
use App\Models\Inscripcion;
use App\Models\User;
use App\Repositories\CursoVisualizacionRepository;
use Illuminate\Support\Carbon;
use RuntimeException;
use Throwable;

class VisualizacionCursoService
{
    public function __construct(
        private readonly CursoVisualizacionRepository $cursoVisualizacionRepository
    ) {
    }

    public function index(User $user, array $filters = [], ?int $selectedId = null, int $perPage = 8): array
    {
        try {
            $courses = $this->cursoVisualizacionRepository->paginateForUser($user, $filters, $perPage);

            $firstItem = $courses->getCollection()->first();
            $selectedId = $selectedId ?: ($firstItem?->IdCursoMateria ?? $firstItem?->IdInscripcion ?? null);
            $selected = $selectedId ? $this->cursoVisualizacionRepository->findForUser($user, (int) $selectedId) : null;

            return [
                'status' => true,
                'message' => 'Visualizacion de cursos cargada correctamente.',
                'data' => [
                    'role' => $this->roleLabel((int) $user->IdRol),
                    'courses' => [
                        'data' => $courses->getCollection()->map(fn ($item) => $this->formatItem($user, $item))->values(),
                        'current_page' => $courses->currentPage(),
                        'last_page' => $courses->lastPage(),
                        'per_page' => $courses->perPage(),
                        'total' => $courses->total(),
                        'from' => $courses->firstItem(),
                        'to' => $courses->lastItem(),
                    ],
                    'selected' => $selected ? $this->formatDetail($user, $selected) : null,
                    'filters' => [
                        'search' => $filters['search'] ?? '',
                        'IdCurso' => $filters['IdCurso'] ?? '',
                        'IdTurno' => $filters['IdTurno'] ?? '',
                    ],
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cargar la visualizacion de cursos.');
        }
    }

    private function roleLabel(int $roleId): string
    {
        return match ($roleId) {
            1 => 'Administrador',
            2 => 'Docente',
            3 => 'Estudiante',
            default => 'Usuario',
        };
    }

    private function formatItem(User $user, mixed $item): array
    {
        if ($item instanceof Inscripcion) {
            $cursoMateria = $item->cursoMateria;

            return [
                'type' => 'inscripcion',
                'IdInscripcion' => $item->IdInscripcion,
                'IdCursoMateria' => $cursoMateria?->IdCursoMateria,
                'Curso' => [
                    'IdCurso' => $cursoMateria?->curso?->IdCurso,
                    'Nombre' => $cursoMateria?->curso?->Nombre,
                ],
                'Materia' => [
                    'IdMateria' => $cursoMateria?->materia?->IdMateria,
                    'Nombre' => $cursoMateria?->materia?->Nombre,
                ],
                'Docente' => [
                    'IdUsuario' => $cursoMateria?->docente?->IdUsuario,
                    'Nombre1' => $cursoMateria?->docente?->Nombre1,
                    'Nombre2' => $cursoMateria?->docente?->Nombre2,
                    'Apellido1' => $cursoMateria?->docente?->Apellido1,
                    'Apellido2' => $cursoMateria?->docente?->Apellido2,
                ],
                'Turno' => [
                    'IdTurno' => $cursoMateria?->turno?->IdTurno,
                    'Nombre' => $cursoMateria?->turno?->Nombre,
                    'Dias' => $cursoMateria?->turno?->Dias,
                ],
                'FechaInscripcion' => optional($item->Fecha)->toISOString(),
                'Estado' => $item->Estado,
            ];
        }

        return [
            'type' => 'curso_materia',
            'IdCursoMateria' => $item->IdCursoMateria,
            'Curso' => [
                'IdCurso' => $item->curso?->IdCurso,
                'Nombre' => $item->curso?->Nombre,
            ],
            'Materia' => [
                'IdMateria' => $item->materia?->IdMateria,
                'Nombre' => $item->materia?->Nombre,
            ],
            'Docente' => [
                'IdUsuario' => $item->docente?->IdUsuario,
                'Nombre1' => $item->docente?->Nombre1,
                'Nombre2' => $item->docente?->Nombre2,
                'Apellido1' => $item->docente?->Apellido1,
                'Apellido2' => $item->docente?->Apellido2,
            ],
            'Turno' => [
                'IdTurno' => $item->turno?->IdTurno,
                'Nombre' => $item->turno?->Nombre,
                'Dias' => $item->turno?->Dias,
            ],
            'FechaInicio' => optional($item->FechaInicio)->toDateString(),
            'FechaFin' => optional($item->FechaFin)->toDateString(),
            'MaxInscritos' => $item->MaxInscritos,
            'InscritosActivos' => (int) ($item->inscritos_activos_count ?? 0),
            'CupoDisponible' => max((int) $item->MaxInscritos - (int) ($item->inscritos_activos_count ?? 0), 0),
            'Estado' => $item->Estado,
        ];
    }

    private function formatDetail(User $user, mixed $item): array
    {
        if ($item instanceof Inscripcion) {
            $cursoMateria = $item->cursoMateria;

            return [
                'type' => 'inscripcion',
                'IdInscripcion' => $item->IdInscripcion,
                'FechaInscripcion' => optional($item->Fecha)->toISOString(),
                'Estado' => $item->Estado,
                'CursoMateria' => $cursoMateria ? [
                    'IdCursoMateria' => $cursoMateria->IdCursoMateria,
                    'Curso' => [
                        'IdCurso' => $cursoMateria->curso?->IdCurso,
                        'Nombre' => $cursoMateria->curso?->Nombre,
                        'Descripcion' => $cursoMateria->curso?->Descripcion,
                    ],
                    'Materia' => [
                        'IdMateria' => $cursoMateria->materia?->IdMateria,
                        'Nombre' => $cursoMateria->materia?->Nombre,
                    ],
                    'Docente' => [
                        'IdUsuario' => $cursoMateria->docente?->IdUsuario,
                        'Nombre1' => $cursoMateria->docente?->Nombre1,
                        'Nombre2' => $cursoMateria->docente?->Nombre2,
                        'Apellido1' => $cursoMateria->docente?->Apellido1,
                        'Apellido2' => $cursoMateria->docente?->Apellido2,
                    ],
                    'Turno' => [
                        'IdTurno' => $cursoMateria->turno?->IdTurno,
                        'Nombre' => $cursoMateria->turno?->Nombre,
                        'HoraInicio' => $cursoMateria->turno?->HoraInicio,
                        'HoraFin' => $cursoMateria->turno?->HoraFin,
                        'Dias' => $cursoMateria->turno?->Dias,
                    ],
                    'FechaInicio' => optional($cursoMateria->FechaInicio)->toDateString(),
                    'FechaFin' => optional($cursoMateria->FechaFin)->toDateString(),
                    'MaxInscritos' => $cursoMateria->MaxInscritos,
                ] : null,
            ];
        }

        $students = collect($item->inscripciones ?? [])
            ->where('Estado', true)
            ->map(function ($inscripcion) {
                return [
                    'IdInscripcion' => $inscripcion->IdInscripcion,
                    'Fecha' => optional($inscripcion->Fecha)->toISOString(),
                    'Estudiante' => [
                        'IdUsuario' => $inscripcion->estudiante?->IdUsuario,
                        'Nombre1' => $inscripcion->estudiante?->Nombre1,
                        'Nombre2' => $inscripcion->estudiante?->Nombre2,
                        'Apellido1' => $inscripcion->estudiante?->Apellido1,
                        'Apellido2' => $inscripcion->estudiante?->Apellido2,
                        'CI' => $inscripcion->estudiante?->CI,
                        'Correo' => $inscripcion->estudiante?->Correo,
                        'Semestre' => $inscripcion->estudiante?->Semestre,
                    ],
                ];
            })
            ->values();

        return [
            'type' => 'curso_materia',
            'IdCursoMateria' => $item->IdCursoMateria,
            'Curso' => [
                'IdCurso' => $item->curso?->IdCurso,
                'Nombre' => $item->curso?->Nombre,
                'Descripcion' => $item->curso?->Descripcion,
                'FechaRegistro' => optional($item->curso?->FechaRegistro)->toISOString(),
            ],
            'Materia' => [
                'IdMateria' => $item->materia?->IdMateria,
                'Nombre' => $item->materia?->Nombre,
                'IdCarrera' => $item->materia?->IdCarrera,
            ],
            'Docente' => [
                'IdUsuario' => $item->docente?->IdUsuario,
                'Nombre1' => $item->docente?->Nombre1,
                'Nombre2' => $item->docente?->Nombre2,
                'Apellido1' => $item->docente?->Apellido1,
                'Apellido2' => $item->docente?->Apellido2,
                'Correo' => $item->docente?->Correo,
            ],
            'Turno' => [
                'IdTurno' => $item->turno?->IdTurno,
                'Nombre' => $item->turno?->Nombre,
                'HoraInicio' => $item->turno?->HoraInicio,
                'HoraFin' => $item->turno?->HoraFin,
                'Dias' => $item->turno?->Dias,
            ],
            'FechaInicio' => optional($item->FechaInicio)->toDateString(),
            'FechaFin' => optional($item->FechaFin)->toDateString(),
            'MaxInscritos' => $item->MaxInscritos,
            'InscritosActivos' => (int) ($item->inscritos_activos_count ?? 0),
            'CupoDisponible' => max((int) $item->MaxInscritos - (int) ($item->inscritos_activos_count ?? 0), 0),
            'Estado' => $item->Estado,
            'Estudiantes' => $students,
        ];
    }
}
