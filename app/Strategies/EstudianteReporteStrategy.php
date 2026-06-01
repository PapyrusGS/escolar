<?php

namespace App\Strategies;

use Illuminate\Support\Facades\DB;

class EstudianteReporteStrategy implements ReporteStrategyInterface
{
    public function getDatos(array $params): array
    {
        $idEstudiante = $params['IdUsuario'] ?? null;

        if (!$idEstudiante) {
            return [];
        }

        // Reporte para Estudiante: Materias inscritas, notas y estado
        return DB::table('inscripciones')
            ->join('cursos', 'inscripciones.IdCurso', '=', 'cursos.IdCurso')
            ->join('cursos_materias', 'cursos.IdCurso', '=', 'cursos_materias.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->leftJoin('notas', function ($join) {
                $join->on('inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
                     ->on('cursos_materias.IdMateria', '=', 'notas.IdMateria');
            })
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'cursos.Nombre as Curso',
                'cursos.Gestion',
                'notas.NotaFinal',
                'notas.Observacion',
                'inscripciones.FechaInscripcion'
            )
            ->where('inscripciones.IdEstudiante', $idEstudiante)
            ->orderBy('inscripciones.FechaInscripcion', 'desc')
            ->get()
            ->toArray();
    }

    public function getTitulo(): string
    {
        return 'Historial Académico e Inscripciones Activas (Estudiante)';
    }
}
