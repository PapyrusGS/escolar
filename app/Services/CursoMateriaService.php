<?php

namespace App\Services;

use App\Repositories\CarreraRepository;
use App\Repositories\CursoMateriaRepository;
use App\Repositories\CursoRepository;
use App\Repositories\MateriaRepository;
use App\Repositories\TurnoRepository;
use App\Models\User;
use RuntimeException;

class CursoMateriaService
{
    public function __construct(
        private readonly CarreraRepository $carreraRepository,
        private readonly CursoMateriaRepository $cursoMateriaRepository,
        private readonly CursoRepository $cursoRepository,
        private readonly MateriaRepository $materiaRepository,
        private readonly TurnoRepository $turnoRepository,
    ) {
    }

    public function all(): array
    {
        return collect($this->cursoMateriaRepository->all())->map(function ($cm) {
            return [
                'IdCursoMateria' => $cm->IdCursoMateria,
                'IdCurso' => $cm->IdCurso,
                'IdMateria' => $cm->IdMateria,
                'IdDocente' => $cm->IdDocente,
                'IdTurno' => $cm->IdTurno,
                'FechaInicio' => $cm->FechaInicio?->toISOString() ?? $cm->FechaInicio,
                'FechaFin' => $cm->FechaFin?->toISOString() ?? $cm->FechaFin,
                'MaxInscritos' => (int) $cm->MaxInscritos,
                'Inscritos' => (int) $cm->Inscritos,
                'Estado' => (bool) $cm->Estado,
                'Curso' => $cm->curso ? [
                    'IdCurso' => $cm->curso->IdCurso,
                    'Piso' => $cm->curso->Piso,
                    'Aula' => $cm->curso->Aula,
                    'Nombre' => $cm->curso->Nombre,
                ] : null,
                'Materia' => $cm->materia ? [
                    'IdMateria' => $cm->materia->IdMateria,
                    'CodigoMateria' => $cm->materia->CodigoMateria,
                    'Nombre' => $cm->materia->Nombre,
                ] : null,
                'Docente' => $cm->docente ? [
                    'IdUsuario' => $cm->docente->IdUsuario,
                    'Nombre' => $cm->docente->Nombre1 . ' ' . $cm->docente->Apellido1,
                    'Correo' => $cm->docente->Correo,
                ] : null,
                'Turno' => $cm->turno ? [
                    'IdTurno' => $cm->turno->IdTurno,
                    'Nombre' => $cm->turno->Nombre,
                    'HoraInicio' => $cm->turno->HoraInicio,
                    'HoraFin' => $cm->turno->HoraFin,
                ] : null,
            ];
        })->all();
    }

    public function formData(): array
    {
        $docentes = User::query()
            ->where('IdRol', 2)
            ->where('Estado', true)
            ->get()
            ->map(fn($user) => [
                'IdUsuario' => $user->IdUsuario,
                'Nombre' => $user->Nombre1 . ' ' . $user->Apellido1,
            ])
            ->all();

        return [
            'carreras' => collect($this->carreraRepository->activeAll())->map(fn($c) => [
                'IdCarrera' => $c->IdCarrera,
                'Nombre' => $c->Nombre,
            ])->all(),
            'cursos' => collect($this->cursoRepository->activeAll())->map(fn($c) => [
                'IdCurso' => $c->IdCurso,
                'Piso' => $c->Piso,
                'Aula' => $c->Aula,
                'Nombre' => $c->Nombre ?? ($c->Aula ? 'Aula ' . $c->Aula : 'Curso ' . $c->IdCurso),
            ])->all(),
            'materias' => collect($this->materiaRepository->activeAll())->map(fn($m) => [
                'IdMateria' => $m->IdMateria,
                'CodigoMateria' => $m->CodigoMateria,
                'Nombre' => $m->Nombre,
            ])->all(),
            'docentes' => $docentes,
            'turnos' => collect($this->turnoRepository->activeAll())->map(fn($t) => [
                'IdTurno' => $t->IdTurno,
                'Nombre' => $t->Nombre,
                'HoraInicio' => $t->HoraInicio,
                'HoraFin' => $t->HoraFin,
            ])->all(),
        ];
    }

    public function store(array $payload): array
    {
        try {
            $payload['FechaRegistro'] = now();
            $payload['Estado'] = true;
            $payload['Inscritos'] = 0;

            $cursoMateria = $this->cursoMateriaRepository->create($payload);

            return [
                'status' => true,
                'message' => 'Curso agendado correctamente.',
                'data' => $cursoMateria,
            ];
        } catch (\Throwable $e) {
            throw new RuntimeException('No se pudo registrar el curso: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(int $id, array $payload): array
    {
        try {
            $this->cursoMateriaRepository->update($id, $payload);
            $cursoMateria = $this->cursoMateriaRepository->findById($id);

            return [
                'status' => true,
                'message' => 'Curso agendado actualizado correctamente.',
                'data' => $cursoMateria,
            ];
        } catch (\Throwable $e) {
            throw new RuntimeException('No se pudo actualizar el curso agendado: ' . $e->getMessage(), 0, $e);
        }
    }

    public function destroy(int $id): array
    {
        try {
            $hasInscriptions = \Illuminate\Support\Facades\DB::table('inscripciones')
                ->where('IdCursoMateria', $id)
                ->exists();

            if ($hasInscriptions) {
                $this->cursoMateriaRepository->update($id, ['Estado' => false]);

                return [
                    'status' => 'deactivated',
                    'message' => 'El curso posee estudiantes inscritos, por lo que ha sido desactivado automáticamente para conservar la integridad académica.',
                ];
            }

            $this->cursoMateriaRepository->delete($id);

            return [
                'status' => 'deleted',
                'message' => 'Curso programado eliminado física y permanentemente por completo.',
            ];
        } catch (\Throwable $e) {
            throw new RuntimeException('No se pudo eliminar el curso: ' . $e->getMessage(), 0, $e);
        }
    }

    public function toggleStatus(int $id): bool
    {
        $cm = $this->cursoMateriaRepository->findById($id);
        if (!$cm) {
            throw new RuntimeException('Curso agendado no encontrado.');
        }

        return $this->cursoMateriaRepository->update($id, [
            'Estado' => !$cm->Estado,
        ]);
    }

    public function getCursosByUsuario(int $idUsuario): array
    {
        $user = User::query()
            ->with('rol')
            ->leftJoin('EstudianteCarrera', 'usuarios.IdUsuario', '=', 'EstudianteCarrera.IdUsuario')
            ->leftJoin('carreras', 'EstudianteCarrera.IdCarrera', '=', 'carreras.IdCarrera')
            ->leftJoin('modalidad', 'EstudianteCarrera.IdModalidad', '=', 'modalidad.IdModalidad')
            ->where('usuarios.IdUsuario', $idUsuario)
            ->select(
                'usuarios.*',
                'carreras.Nombre as CarreraNombre',
                'modalidad.Nombre as ModalidadNombre'
            )
            ->first();

        if (!$user) {
            throw new \RuntimeException('Usuario no encontrado.');
        }

        $userProfile = [
            'IdUsuario' => $user->IdUsuario,
            'Nombre' => trim($user->Nombre1 . ' ' . $user->Nombre2) . ' ' . trim($user->Apellido1 . ' ' . $user->Apellido2),
            'Correo' => $user->Correo,
            'CI' => $user->CI,
            'Telefono' => $user->Telefono,
            'Rol' => $user->rol?->Nombre,
            'IdRol' => (int) $user->IdRol,
            'Carrera' => $user->CarreraNombre,
            'Modalidad' => $user->ModalidadNombre,
        ];

        $coursesFormatted = [];

        if ($user->IdRol === 2) {
            // Docente: Obtener cursos programados bajo su docencia
            $courses = \Illuminate\Support\Facades\DB::table('cursos_materias')
                ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
                ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
                ->join('turnos', 'cursos_materias.IdTurno', '=', 'turnos.IdTurno')
                ->where('cursos_materias.IdDocente', $idUsuario)
                ->select(
                    'cursos_materias.IdCursoMateria',
                    'cursos_materias.FechaInicio',
                    'cursos_materias.FechaFin',
                    'cursos_materias.MaxInscritos',
                    'cursos_materias.Inscritos',
                    'cursos_materias.Estado as CursoEstado',
                    'cursos.Aula',
                    'cursos.Piso',
                    'cursos.Nombre as AulaNombre',
                    'materias.Nombre as MateriaNombre',
                    'materias.CodigoMateria',
                    'turnos.Nombre as TurnoNombre',
                    'turnos.HoraInicio',
                    'turnos.HoraFin'
                )
                ->get();

            $coursesFormatted = collect($courses)->map(function ($c) {
                // Obtener alumnos inscritos en este curso específico
                $enrolled = \Illuminate\Support\Facades\DB::table('inscripciones')
                    ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
                    ->join('usuarios', 'EstudianteCarrera.IdUsuario', '=', 'usuarios.IdUsuario')
                    ->join('carreras', 'EstudianteCarrera.IdCarrera', '=', 'carreras.IdCarrera')
                    ->join('modalidad', 'EstudianteCarrera.IdModalidad', '=', 'modalidad.IdModalidad')
                    ->where('inscripciones.IdCursoMateria', $c->IdCursoMateria)
                    ->select(
                        'usuarios.IdUsuario',
                        'usuarios.Nombre1',
                        'usuarios.Nombre2',
                        'usuarios.Apellido1',
                        'usuarios.Apellido2',
                        'usuarios.CI',
                        'usuarios.Correo',
                        'carreras.Nombre as Carrera',
                        'modalidad.Nombre as Modalidad',
                        'inscripciones.Fecha as FechaInscripcion',
                        'inscripciones.Estado as EstadoInscripcion',
                        'inscripciones.Aprobado'
                    )
                    ->get();

                $enrolledFormatted = collect($enrolled)->map(function ($stu) {
                    return [
                        'IdUsuario' => $stu->IdUsuario,
                        'Nombre' => trim($stu->Nombre1 . ' ' . $stu->Nombre2) . ' ' . trim($stu->Apellido1 . ' ' . $stu->Apellido2),
                        'CI' => $stu->CI,
                        'Correo' => $stu->Correo,
                        'Carrera' => $stu->Carrera,
                        'Modalidad' => $stu->Modalidad,
                        'FechaInscripcion' => $stu->FechaInscripcion,
                        'Estado' => (bool)$stu->EstadoInscripcion,
                        'Aprobado' => (bool)$stu->Aprobado,
                    ];
                })->all();

                return [
                    'IdCursoMateria' => $c->IdCursoMateria,
                    'FechaInicio' => $c->FechaInicio,
                    'FechaFin' => $c->FechaFin,
                    'MaxInscritos' => (int)$c->MaxInscritos,
                    'Inscritos' => (int)$c->Inscritos,
                    'Estado' => (bool)$c->CursoEstado,
                    'Materia' => [
                        'Nombre' => $c->MateriaNombre,
                        'CodigoMateria' => $c->CodigoMateria,
                    ],
                    'Curso' => [
                        'Aula' => $c->Aula,
                        'Piso' => $c->Piso,
                        'Nombre' => $c->AulaNombre ?? ('Aula ' . $c->Aula),
                    ],
                    'Turno' => [
                        'Nombre' => $c->TurnoNombre,
                        'HoraInicio' => $c->HoraInicio,
                        'HoraFin' => $c->HoraFin,
                    ],
                    'Alumnos' => $enrolledFormatted
                ];
            })->all();
        } elseif ($user->IdRol === 3) {
            // Estudiante: Obtener cursos en los que se encuentra inscrito
            $courses = \Illuminate\Support\Facades\DB::table('inscripciones')
                ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
                ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
                ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
                ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
                ->join('usuarios as docente', 'cursos_materias.IdDocente', '=', 'docente.IdUsuario')
                ->join('turnos', 'cursos_materias.IdTurno', '=', 'turnos.IdTurno')
                ->where('EstudianteCarrera.IdUsuario', $idUsuario)
                ->select(
                    'cursos_materias.IdCursoMateria',
                    'cursos_materias.FechaInicio',
                    'cursos_materias.FechaFin',
                    'cursos_materias.Estado as CursoEstado',
                    'cursos.Aula',
                    'cursos.Piso',
                    'cursos.Nombre as AulaNombre',
                    'materias.Nombre as MateriaNombre',
                    'materias.CodigoMateria',
                    'docente.Nombre1 as DocenteNombre1',
                    'docente.Nombre2 as DocenteNombre2',
                    'docente.Apellido1 as DocenteApellido1',
                    'docente.Apellido2 as DocenteApellido2',
                    'docente.Correo as DocenteCorreo',
                    'turnos.Nombre as TurnoNombre',
                    'turnos.HoraInicio',
                    'turnos.HoraFin',
                    'inscripciones.Fecha as FechaInscripcion',
                    'inscripciones.Estado as EstadoInscripcion',
                    'inscripciones.Aprobado'
                )
                ->get();

            $coursesFormatted = collect($courses)->map(function ($c) {
                return [
                    'IdCursoMateria' => $c->IdCursoMateria,
                    'FechaInicio' => $c->FechaInicio,
                    'FechaFin' => $c->FechaFin,
                    'Estado' => (bool)$c->CursoEstado,
                    'Materia' => [
                        'Nombre' => $c->MateriaNombre,
                        'CodigoMateria' => $c->CodigoMateria,
                    ],
                    'Curso' => [
                        'Aula' => $c->Aula,
                        'Piso' => $c->Piso,
                        'Nombre' => $c->AulaNombre ?? ('Aula ' . $c->Aula),
                    ],
                    'Docente' => [
                        'Nombre' => trim($c->DocenteNombre1 . ' ' . $c->DocenteNombre2) . ' ' . trim($c->DocenteApellido1 . ' ' . $c->DocenteApellido2),
                        'Correo' => $c->DocenteCorreo,
                    ],
                    'Turno' => [
                        'Nombre' => $c->TurnoNombre,
                        'HoraInicio' => $c->HoraInicio,
                        'HoraFin' => $c->HoraFin,
                    ],
                    'Inscripcion' => [
                        'Fecha' => $c->FechaInscripcion,
                        'Estado' => (bool)$c->EstadoInscripcion,
                        'Aprobado' => (bool)$c->Aprobado,
                    ]
                ];
            })->all();
        }

        return [
            'usuario' => $userProfile,
            'cursos' => $coursesFormatted,
        ];
    }
}
