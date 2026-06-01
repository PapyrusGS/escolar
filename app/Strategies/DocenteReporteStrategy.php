<?php

namespace App\Strategies;

use Illuminate\Support\Facades\DB;

class DocenteReporteStrategy implements ReporteStrategyInterface
{
    public function getDatos(array $params): array
    {
        $idDocente = $params['IdUsuario'] ?? null;

        if (!$idDocente) {
            return [];
        }

        // Reporte para Docente: Materias dictadas y total de alumnos inscritos por curso
        return DB::table('cursos_materias')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->leftJoin('inscripciones', 'cursos.IdCurso', '=', 'inscripciones.IdCurso')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'cursos.Nombre as Curso',
                'cursos.Gestion',
                DB::raw('COUNT(inscripciones.IdInscripcion) as TotalEstudiantes')
            )
            ->where('cursos_materias.IdDocente', $idDocente)
            ->groupBy('materias.CodigoMateria', 'materias.Nombre', 'cursos.Nombre', 'cursos.Gestion', 'cursos.IdCurso')
            ->get()
            ->toArray();
    }

    public function getTitulo(): string
    {
        return 'Reporte de Cursos y Estudiantes Asignados (Docente)';
    }
}
