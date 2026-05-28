<?php

namespace App\Services;

use App\Models\CursoMateria;
use App\Models\Inscripcion;
use App\Models\User;
use App\Repositories\CursoMateriaRepository;
use App\Repositories\InscripcionRepository;
use Illuminate\Support\Carbon;
use RuntimeException;
use Throwable;

class InscripcionService
{
    public function __construct(
        private readonly CursoMateriaRepository $cursoMateriaRepository,
        private readonly InscripcionRepository $inscripcionRepository
    ) {
    }

    public function index(User $student, array $filters = [], int $perPage = 10): array
    {
        try {
            $available = $this->cursoMateriaRepository->paginateAvailableForStudent(
                $student->IdUsuario,
                $student->IdCarrera,
                $filters,
                $perPage
            );

            $myEnrollments = $this->inscripcionRepository->myActiveEnrollments($student->IdUsuario);

            return [
                'status' => true,
                'message' => 'Catalogo de inscripciones cargado correctamente.',
                'data' => [
                    'available' => [
                        'data' => $available->getCollection()->map(fn (CursoMateria $cursoMateria) => $this->formatAvailableCourseMateria($cursoMateria))->values(),
                        'current_page' => $available->currentPage(),
                        'last_page' => $available->lastPage(),
                        'per_page' => $available->perPage(),
                        'total' => $available->total(),
                        'from' => $available->firstItem(),
                        'to' => $available->lastItem(),
                    ],
                    'my_inscriptions' => $myEnrollments->map(fn (Inscripcion $inscripcion) => $this->formatMyEnrollment($inscripcion))->values(),
                    'filters' => [
                        'search' => $filters['search'] ?? '',
                        'IdCurso' => $filters['IdCurso'] ?? '',
                        'IdTurno' => $filters['IdTurno'] ?? '',
                    ],
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cargar el catalogo de inscripciones.');
        }
    }

    public function store(User $student, array $payload): array
    {
        try {
            if ($student->IdRol !== 3) {
                return [
                    'status' => false,
                    'message' => 'Solo los estudiantes pueden inscribirse a cursos.',
                    'data' => [],
                ];
            }

            $cursoMateria = $this->cursoMateriaRepository->findById((int) $payload['IdCursoMateria']);

            if (! $cursoMateria || ! $cursoMateria->Estado) {
                return [
                    'status' => false,
                    'message' => 'El curso seleccionado no esta disponible para inscripcion.',
                    'data' => [],
                ];
            }

            if ($student->IdCarrera && (int) $cursoMateria->materia?->IdCarrera !== (int) $student->IdCarrera) {
                return [
                    'status' => false,
                    'message' => 'El curso no pertenece a tu carrera.',
                    'data' => [],
                ];
            }

            $existing = $this->inscripcionRepository->findByStudentAndCursoMateria($student->IdUsuario, $cursoMateria->IdCursoMateria);

            if ($existing?->Estado) {
                return [
                    'status' => false,
                    'message' => 'Ya estas inscrito en este curso.',
                    'data' => [],
                ];
            }

            $activeCount = $this->inscripcionRepository->countActiveByCursoMateria($cursoMateria->IdCursoMateria);

            if ($activeCount >= $cursoMateria->MaxInscritos) {
                return [
                    'status' => false,
                    'message' => 'El curso alcanzo su cupo maximo.',
                    'data' => [],
                ];
            }

            if ($existing) {
                $inscripcion = $this->inscripcionRepository->update($existing, [
                    'Estado' => true,
                    'Fecha' => Carbon::now(),
                ]);

                $message = 'Inscripcion reactivada correctamente.';
            } else {
                $inscripcion = $this->inscripcionRepository->create([
                    'IdEstudiante' => $student->IdUsuario,
                    'IdCursoMateria' => $cursoMateria->IdCursoMateria,
                    'Fecha' => Carbon::now(),
                    'Estado' => true,
                ]);

                $message = 'Inscripcion realizada correctamente.';
            }

            $inscripcion->load([
                'estudiante.rol',
                'cursoMateria.curso',
                'cursoMateria.materia',
                'cursoMateria.docente.rol',
                'cursoMateria.turno',
            ]);

            return [
                'status' => true,
                'message' => $message,
                'data' => [
                    'inscripcion' => $this->formatMyEnrollment($inscripcion),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo realizar la inscripcion.');
        }
    }

    public function teacherDashboard(User $docente, array $filters = [], ?int $cursoMateriaId = null, int $perPage = 10): array
    {
        try {
            if ($docente->IdRol !== 2) {
                return [
                    'status' => false,
                    'message' => 'Solo los docentes pueden acceder a este modulo.',
                    'data' => [],
                ];
            }

            $assignments = $this->cursoMateriaRepository->teacherAssignments($docente->IdUsuario, [], $perPage);
            $assignmentsList = $this->cursoMateriaRepository->teacherAssignmentsList($docente->IdUsuario);

            $selectedId = $cursoMateriaId ?: $assignmentsList->first()?->IdCursoMateria;
            $selectedAssignment = null;
            $students = [
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => $perPage,
                'total' => 0,
                'from' => null,
                'to' => null,
            ];

            if ($selectedId) {
                $selectedAssignment = $assignmentsList->firstWhere('IdCursoMateria', (int) $selectedId);

                if ($selectedAssignment) {
                    $studentsPaginator = $this->inscripcionRepository->studentsByCursoMateria((int) $selectedId, $filters, $perPage);
                    $students = [
                        'data' => $studentsPaginator->getCollection()->map(fn (Inscripcion $inscripcion) => $this->formatTeacherStudent($inscripcion))->values(),
                        'current_page' => $studentsPaginator->currentPage(),
                        'last_page' => $studentsPaginator->lastPage(),
                        'per_page' => $studentsPaginator->perPage(),
                        'total' => $studentsPaginator->total(),
                        'from' => $studentsPaginator->firstItem(),
                        'to' => $studentsPaginator->lastItem(),
                    ];
                }
            }

            return [
                'status' => true,
                'message' => 'Panel del docente cargado correctamente.',
                'data' => [
                    'assignments' => [
                        'data' => $assignments->getCollection()->map(fn (CursoMateria $cursoMateria) => $this->formatTeacherAssignment($cursoMateria))->values(),
                        'current_page' => $assignments->currentPage(),
                        'last_page' => $assignments->lastPage(),
                        'per_page' => $assignments->perPage(),
                        'total' => $assignments->total(),
                        'from' => $assignments->firstItem(),
                        'to' => $assignments->lastItem(),
                    ],
                    'selected_assignment' => $selectedAssignment ? $this->formatTeacherAssignment($selectedAssignment) : null,
                    'students' => $students,
                    'filters' => [
                        'search' => $filters['search'] ?? '',
                    ],
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cargar el panel del docente.');
        }
    }

    public function withdrawStudent(User $docente, Inscripcion $inscripcion): array
    {
        try {
            $inscripcion->load([
                'cursoMateria.curso',
                'cursoMateria.materia',
                'cursoMateria.docente.rol',
                'cursoMateria.turno',
                'estudiante.rol',
            ]);

            if ($docente->IdRol !== 2) {
                return [
                    'status' => false,
                    'message' => 'Solo los docentes pueden retirar estudiantes.',
                    'data' => [],
                ];
            }

            if ((int) $inscripcion->cursoMateria?->IdDocente !== (int) $docente->IdUsuario) {
                return [
                    'status' => false,
                    'message' => 'No puedes retirar estudiantes de cursos que no te pertenecen.',
                    'data' => [],
                ];
            }

            if (! $inscripcion->Estado) {
                return [
                    'status' => false,
                    'message' => 'La inscripcion ya estaba inactiva.',
                    'data' => [],
                ];
            }

            $inscripcion = $this->inscripcionRepository->update($inscripcion, [
                'Estado' => false,
            ]);

            $inscripcion->load([
                'estudiante.rol',
                'cursoMateria.curso',
                'cursoMateria.materia',
                'cursoMateria.docente.rol',
                'cursoMateria.turno',
            ]);

            return [
                'status' => true,
                'message' => 'Estudiante retirado correctamente del curso.',
                'data' => [
                    'inscripcion' => $this->formatTeacherStudent($inscripcion),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo retirar al estudiante del curso.');
        }
    }

    private function formatAvailableCourseMateria(CursoMateria $cursoMateria): array
    {
        $inscritos = (int) ($cursoMateria->inscritos_activos_count ?? 0);
        $cupoDisponible = max((int) $cursoMateria->MaxInscritos - $inscritos, 0);

        return [
            'IdCursoMateria' => $cursoMateria->IdCursoMateria,
            'Curso' => [
                'IdCurso' => $cursoMateria->curso?->IdCurso,
                'Nombre' => $cursoMateria->curso?->Nombre,
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
            'InscritosActivos' => $inscritos,
            'CupoDisponible' => $cupoDisponible,
            'Estado' => $cursoMateria->Estado,
        ];
    }

    private function formatMyEnrollment(Inscripcion $inscripcion): array
    {
        return [
            'IdInscripcion' => $inscripcion->IdInscripcion,
            'Fecha' => optional($inscripcion->Fecha)->toISOString(),
            'Estado' => $inscripcion->Estado,
            'CursoMateria' => [
                'IdCursoMateria' => $inscripcion->cursoMateria?->IdCursoMateria,
                'Curso' => [
                    'IdCurso' => $inscripcion->cursoMateria?->curso?->IdCurso,
                    'Nombre' => $inscripcion->cursoMateria?->curso?->Nombre,
                ],
                'Materia' => [
                    'IdMateria' => $inscripcion->cursoMateria?->materia?->IdMateria,
                    'Nombre' => $inscripcion->cursoMateria?->materia?->Nombre,
                ],
                'Docente' => [
                    'IdUsuario' => $inscripcion->cursoMateria?->docente?->IdUsuario,
                    'Nombre1' => $inscripcion->cursoMateria?->docente?->Nombre1,
                    'Nombre2' => $inscripcion->cursoMateria?->docente?->Nombre2,
                    'Apellido1' => $inscripcion->cursoMateria?->docente?->Apellido1,
                    'Apellido2' => $inscripcion->cursoMateria?->docente?->Apellido2,
                ],
                'Turno' => [
                    'IdTurno' => $inscripcion->cursoMateria?->turno?->IdTurno,
                    'Nombre' => $inscripcion->cursoMateria?->turno?->Nombre,
                    'HoraInicio' => $inscripcion->cursoMateria?->turno?->HoraInicio,
                    'HoraFin' => $inscripcion->cursoMateria?->turno?->HoraFin,
                    'Dias' => $inscripcion->cursoMateria?->turno?->Dias,
                ],
                'FechaInicio' => optional($inscripcion->cursoMateria?->FechaInicio)->toDateString(),
                'FechaFin' => optional($inscripcion->cursoMateria?->FechaFin)->toDateString(),
                'MaxInscritos' => $inscripcion->cursoMateria?->MaxInscritos,
            ],
        ];
    }

    private function formatTeacherAssignment(CursoMateria $cursoMateria): array
    {
        return [
            'IdCursoMateria' => $cursoMateria->IdCursoMateria,
            'Curso' => [
                'IdCurso' => $cursoMateria->curso?->IdCurso,
                'Nombre' => $cursoMateria->curso?->Nombre,
            ],
            'Materia' => [
                'IdMateria' => $cursoMateria->materia?->IdMateria,
                'Nombre' => $cursoMateria->materia?->Nombre,
            ],
            'Turno' => [
                'IdTurno' => $cursoMateria->turno?->IdTurno,
                'Nombre' => $cursoMateria->turno?->Nombre,
                'Dias' => $cursoMateria->turno?->Dias,
            ],
            'MaxInscritos' => $cursoMateria->MaxInscritos,
            'InscritosActivos' => (int) ($cursoMateria->inscritos_activos_count ?? 0),
            'Estado' => $cursoMateria->Estado,
        ];
    }

    private function formatTeacherStudent(Inscripcion $inscripcion): array
    {
        return [
            'IdInscripcion' => $inscripcion->IdInscripcion,
            'Fecha' => optional($inscripcion->Fecha)->toISOString(),
            'Estado' => $inscripcion->Estado,
            'Estudiante' => [
                'IdUsuario' => $inscripcion->estudiante?->IdUsuario,
                'Nombre1' => $inscripcion->estudiante?->Nombre1,
                'Nombre2' => $inscripcion->estudiante?->Nombre2,
                'Apellido1' => $inscripcion->estudiante?->Apellido1,
                'Apellido2' => $inscripcion->estudiante?->Apellido2,
                'CI' => $inscripcion->estudiante?->CI,
                'Correo' => $inscripcion->estudiante?->Correo,
                'Semestre' => $inscripcion->estudiante?->Semestre,
                'Estado' => $inscripcion->estudiante?->Estado,
            ],
            'CursoMateria' => [
                'IdCursoMateria' => $inscripcion->cursoMateria?->IdCursoMateria,
                'Curso' => [
                    'IdCurso' => $inscripcion->cursoMateria?->curso?->IdCurso,
                    'Nombre' => $inscripcion->cursoMateria?->curso?->Nombre,
                ],
                'Materia' => [
                    'IdMateria' => $inscripcion->cursoMateria?->materia?->IdMateria,
                    'Nombre' => $inscripcion->cursoMateria?->materia?->Nombre,
                ],
                'Turno' => [
                    'IdTurno' => $inscripcion->cursoMateria?->turno?->IdTurno,
                    'Nombre' => $inscripcion->cursoMateria?->turno?->Nombre,
                    'Dias' => $inscripcion->cursoMateria?->turno?->Dias,
                ],
            ],
        ];
    }
}
