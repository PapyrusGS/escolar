<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EstudianteNotasService
{
    public function obtenerNotasPorEstudiante(int $idUsuario): array
    {
        $estudianteCarrera = DB::table('EstudianteCarrera')
            ->where('IdUsuario', $idUsuario)
            ->where('Estado', 1)
            ->first();

        if (! $estudianteCarrera) {
            return [
                'estudiante' => null,
                'notas' => [],
                'promedio' => 0,
                'aprobadas' => 0,
                'reprobadas' => 0,
                'sinNota' => 0,
                'total' => 0,
            ];
        }

        $rows = DB::table('inscripciones as i')
            ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
            ->join('materias as m', 'cm.IdMateria', '=', 'm.IdMateria')
            ->join('cursos as c', 'cm.IdCurso', '=', 'c.IdCurso')
            ->join('turnos as t', 'cm.IdTurno', '=', 't.IdTurno')
            ->join('usuarios as d', 'cm.IdDocente', '=', 'd.IdUsuario')
            ->leftJoin('notas as n', function ($join) {
                $join->on('n.IdInscripcion', '=', 'i.IdInscripcion')
                    ->where('n.Estado', 1);
            })
            ->where('i.IdEstudiante', $estudianteCarrera->IdEstudianteCarrera)
            ->where('i.Estado', 1)
            ->orderBy('m.Nombre')
            ->select(
                'i.IdInscripcion',
                'cm.IdCursoMateria',
                'm.Nombre as Materia',
                'm.CodigoMateria',
                DB::raw("CONCAT(d.Nombre1, ' ', d.Apellido1) as Docente"),
                'c.Nombre as Aula',
                'c.Piso',
                't.Nombre as Turno',
                'cm.FechaInicio',
                'cm.FechaFin',
                'i.Aprobado',
                'i.Fecha as FechaInscripcion',
                'n.Nota',
                'n.IdNota'
            )
            ->get();

        $notas = [];
        $sumaNotas = 0;
        $countNotas = 0;
        $aprobadas = 0;
        $reprobadas = 0;
        $sinNota = 0;

        foreach ($rows as $r) {
            $tieneNota = $r->Nota !== null;
            if ($tieneNota) {
                $sumaNotas += (float) $r->Nota;
                $countNotas++;
                if ((float) $r->Nota >= 51) {
                    $aprobadas++;
                } else {
                    $reprobadas++;
                }
            } else {
                $sinNota++;
            }

            $notas[] = [
                'IdInscripcion' => (int) $r->IdInscripcion,
                'IdCursoMateria' => (int) $r->IdCursoMateria,
                'Materia' => $r->Materia,
                'CodigoMateria' => $r->CodigoMateria,
                'Docente' => $r->Docente,
                'Aula' => $r->Aula ?? ('Aula ' . $r->Aula),
                'Piso' => $r->Piso,
                'Turno' => $r->Turno,
                'FechaInicio' => $r->FechaInicio,
                'FechaFin' => $r->FechaFin,
                'FechaInscripcion' => $r->FechaInscripcion,
                'Nota' => $r->Nota !== null ? (float) $r->Nota : null,
                'Aprobado' => (bool) $r->Aprobado,
                'Estado' => $tieneNota ? (((float) $r->Nota >= 51) ? 'Aprobada' : 'Reprobada') : 'Sin nota',
            ];
        }

        return [
            'estudiante' => [
                'IdEstudianteCarrera' => (int) $estudianteCarrera->IdEstudianteCarrera,
            ],
            'notas' => $notas,
            'total' => count($notas),
            'aprobadas' => $aprobadas,
            'reprobadas' => $reprobadas,
            'sinNota' => $sinNota,
            'promedio' => $countNotas > 0 ? round($sumaNotas / $countNotas, 2) : 0,
        ];
    }
}
