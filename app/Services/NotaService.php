<?php

namespace App\Services;

use App\Repositories\MateriaAprobadaRepository;
use App\Repositories\NotaRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class NotaService
{
    private const DIAS_PARA_EDITAR = 7;

    public function __construct(
        private readonly NotaRepository $notaRepository,
        private readonly MateriaAprobadaRepository $materiaAprobadaRepository,
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
        $inscripcion = DB::table('inscripciones')
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

        DB::table('inscripciones')
            ->where('IdInscripcion', $idInscripcion)
            ->update(['Aprobado' => $aprobado]);

        $this->syncAprobacion($idInscripcion, $nota);

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

        $fechaLimite = Carbon::parse($notaRecord->FechaRegistro)->addDays(self::DIAS_PARA_EDITAR);
        if (Carbon::now()->greaterThan($fechaLimite)) {
            throw new RuntimeException(
                'La nota ya no puede modificarse. Han pasado más de ' . self::DIAS_PARA_EDITAR . ' días desde su registro.'
            );
        }

        $inscripcion = DB::table('inscripciones')
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

        DB::table('inscripciones')
            ->where('IdInscripcion', $notaRecord->IdInscripcion)
            ->update(['Aprobado' => $aprobado]);

        $this->syncAprobacion($notaRecord->IdInscripcion, $nota);

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

        $distribucion = [
            '0_50'   => $alumnos->filter(fn($a) => $a->Nota !== null && (float) $a->Nota >= 0  && (float) $a->Nota <= 50)->count(),
            '51_70'  => $alumnos->filter(fn($a) => $a->Nota !== null && (float) $a->Nota >= 51 && (float) $a->Nota <= 70)->count(),
            '71_90'  => $alumnos->filter(fn($a) => $a->Nota !== null && (float) $a->Nota >= 71 && (float) $a->Nota <= 90)->count(),
            '91_100' => $alumnos->filter(fn($a) => $a->Nota !== null && (float) $a->Nota >= 91 && (float) $a->Nota <= 100)->count(),
        ];

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
            'distribucion' => $distribucion,
        ];
    }

    public function obtenerCursosDelDocente(int $idDocente): array
    {
        return $this->notaRepository->getByIdDocente($idDocente);
    }

    private function syncAprobacion(int $idInscripcion, float $nuevaNota): void
    {
        [$idUsuario, $idMateria, $idCarrera] = $this->resolveEstudianteYMateria($idInscripcion);

        if ($nuevaNota >= 51) {
            $this->materiaAprobadaRepository->upsertOnApproval($idUsuario, $idMateria, $idCarrera, $nuevaNota);
        } else {
            $this->materiaAprobadaRepository->deleteOnRevocation($idUsuario, $idMateria);
        }
    }

    private function resolveEstudianteYMateria(int $idInscripcion): array
    {
        $row = DB::table('inscripciones as i')
            ->join('EstudianteCarrera as ec', 'i.IdEstudiante', '=', 'ec.IdEstudianteCarrera')
            ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
            ->where('i.IdInscripcion', $idInscripcion)
            ->select('ec.IdUsuario', 'cm.IdMateria', 'ec.IdCarrera')
            ->first();

        if (!$row) {
            throw new RuntimeException('No se pudo resolver la inscripción para sincronizar la aprobación.');
        }

        return [(int) $row->IdUsuario, (int) $row->IdMateria, (int) $row->IdCarrera];
    }

    private function validarCursoDelDocente(int $idDocente, int $idCursoMateria): void
    {
        $existe = DB::table('cursos_materias')
            ->where('IdCursoMateria', $idCursoMateria)
            ->where('IdDocente', $idDocente)
            ->exists();

        if (!$existe) {
            throw new RuntimeException('El curso especificado no está asignado a tu docencia.');
        }
    }
}
