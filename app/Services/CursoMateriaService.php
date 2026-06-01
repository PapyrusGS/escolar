<?php

namespace App\Services;

use App\Repositories\CursoMateriaRepository;
use App\Repositories\CursoRepository;
use App\Repositories\MateriaRepository;
use App\Repositories\TurnoRepository;
use App\Models\User;
use RuntimeException;

class CursoMateriaService
{
    public function __construct(
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
}
