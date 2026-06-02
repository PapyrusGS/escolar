<?php

namespace App\Services;

use App\Repositories\NotaRepository;
use RuntimeException;

class NotaService
{
    public function __construct(
        private readonly NotaRepository $notaRepository,
    ) {
    }

    public function listarNotasPorCurso(int $idDocente, int $idCursoMateria): array
    {
        $this->validarCursoDelDocente($idDocente, $idCursoMateria);

        $alumnos = $this->notaRepository->getByCursoMateria($idCursoMateria);

        return $alumnos->map(fn($a) => [
            'IdInscripcion' => $a->IdInscripcion,
            'IdUsuario' => $a->IdUsuario,
            'Estudiante' => trim($a->Estudiante),
            'CI' => $a->CI,
            'IdNota' => $a->IdNota,
            'Nota' => $a->Nota !== null ? round((float) $a->Nota, 2) : null,
            'Aprobado' => (bool) $a->Aprobado,
            'EstadoNota' => $a->Nota !== null,
        ])->all();
    }

    public function asignarNota(int $idDocente, int $idInscripcion, float $nota): array
    {
        $inscripcion = \Illuminate\Support\Facades\DB::table('inscripciones')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->where('inscripciones.IdInscripcion', $idInscripcion)
            ->where('cursos_materias.IdDocente', $idDocente)
            ->first();

        if (!$inscripcion) {
            throw new RuntimeException('La inscripción no pertenece a un curso de tu docencia.');
        }

        $existente = $this->notaRepository->findByInscripcion($idInscripcion);

        if ($existente) {
            throw new RuntimeException('Esta inscripción ya tiene una nota registrada. Usa editar para modificarla.');
        }

        $aprobado = $nota >= 51;

        $notaRecord = $this->notaRepository->create([
            'IdInscripcion' => $idInscripcion,
            'Nota' => $nota,
            'FechaRegistro' => now(),
            'Estado' => true,
        ]);

        \Illuminate\Support\Facades\DB::table('inscripciones')
            ->where('IdInscripcion', $idInscripcion)
            ->update(['Aprobado' => $aprobado]);

        return [
            'status' => true,
            'message' => 'Nota asignada correctamente.',
            'data' => [
                'IdNota' => $notaRecord->IdNota,
                'Nota' => $nota,
                'Aprobado' => $aprobado,
            ],
        ];
    }

    public function editarNota(int $idDocente, int $idNota, float $nota): array
    {
        $notaRecord = $this->notaRepository->find($idNota);

        if (!$notaRecord) {
            throw new RuntimeException('La nota especificada no existe.');
        }

        $inscripcion = \Illuminate\Support\Facades\DB::table('inscripciones')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->where('inscripciones.IdInscripcion', $notaRecord->IdInscripcion)
            ->where('cursos_materias.IdDocente', $idDocente)
            ->first();

        if (!$inscripcion) {
            throw new RuntimeException('Esta nota no pertenece a un curso de tu docencia.');
        }

        $aprobado = $nota >= 51;

        $this->notaRepository->update($idNota, [
            'Nota' => $nota,
        ]);

        \Illuminate\Support\Facades\DB::table('inscripciones')
            ->where('IdInscripcion', $notaRecord->IdInscripcion)
            ->update(['Aprobado' => $aprobado]);

        return [
            'status' => true,
            'message' => 'Nota actualizada correctamente.',
            'data' => [
                'IdNota' => $idNota,
                'Nota' => $nota,
                'Aprobado' => $aprobado,
            ],
        ];
    }

    public function visualizarRendimiento(int $idDocente, int $idCursoMateria): array
    {
        $this->validarCursoDelDocente($idDocente, $idCursoMateria);

        $alumnos = $this->notaRepository->getByCursoMateria($idCursoMateria);
        $total = $alumnos->count();
        $conNota = $alumnos->where('Nota', '!=', null);
        $aprobados = $alumnos->where('Aprobado', true)->count();
        $reprobados = $alumnos->where('Aprobado', false)->where('Nota', '!=', null)->count();
        $promedio = $conNota->count() > 0
            ? round($conNota->sum('Nota') / $conNota->count(), 2)
            : 0;
        $maxNota = $conNota->max('Nota') ? round((float) $conNota->max('Nota'), 2) : null;
        $minNota = $conNota->min('Nota') ? round((float) $conNota->min('Nota'), 2) : null;

        return [
            'total_alumnos' => $total,
            'alumnos_con_nota' => $conNota->count(),
            'aprobados' => $aprobados,
            'reprobados' => $reprobados,
            'promedio' => $promedio,
            'nota_maxima' => $maxNota,
            'nota_minima' => $minNota,
            'porcentaje_aprobacion' => $conNota->count() > 0
                ? round(($aprobados / $conNota->count()) * 100, 1)
                : 0,
        ];
    }

    public function obtenerCursosDelDocente(int $idDocente): array
    {
        return $this->notaRepository->getByIdDocente($idDocente);
    }

    private function validarCursoDelDocente(int $idDocente, int $idCursoMateria): void
    {
        $existe = \Illuminate\Support\Facades\DB::table('cursos_materias')
            ->where('IdCursoMateria', $idCursoMateria)
            ->where('IdDocente', $idDocente)
            ->exists();

        if (!$existe) {
            throw new RuntimeException('El curso especificado no está asignado a tu docencia.');
        }
    }
}
