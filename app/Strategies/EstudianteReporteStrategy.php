<?php

namespace App\Strategies;

use Illuminate\Support\Facades\DB;

class EstudianteReporteStrategy implements ReporteStrategyInterface
{
    public function getTiposReportes(): array
    {
        return [
            'historial_estudiante' => 'Historial académico del alumno',
            'notas_todas_materias' => 'Notas de todas materias',
            'materias_estudiante' => 'Listado de materias del alumno',
        ];
    }

    public function getTitulo(string $tipoReporte): string
    {
        $titulos = [
            'historial_estudiante' => 'Historial Académico del Alumno',
            'notas_todas_materias' => 'Notas de Todas las Materias',
            'materias_estudiante' => 'Listado de Materias del Alumno',
        ];
        return $titulos[$tipoReporte] ?? 'Reporte de Estudiante';
    }

    public function getFiltros(string $tipoReporte): array
    {
        return [];
    }

    public function getDatos(string $tipoReporte, array $params): array
    {
        $idEstudiante = $params['IdUsuario'] ?? null;
        if (!$idEstudiante) {
            return [];
        }

        switch ($tipoReporte) {
            case 'historial_estudiante':
                return $this->historialEstudiante($idEstudiante);
            case 'notas_todas_materias':
                return $this->notasTodasMaterias($idEstudiante);
            case 'materias_estudiante':
                return $this->materiasEstudiante($idEstudiante);
            default:
                return [];
        }
    }

    private function historialEstudiante(int $idEstudiante): array
    {
        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                'materias.Nombre as Curso',
                'notas.Nota',
                'inscripciones.Aprobado',
                'inscripciones.Fecha'
            )
            ->where('EstudianteCarrera.IdUsuario', $idEstudiante)
            ->orderBy('inscripciones.Fecha', 'desc')
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function notasTodasMaterias(int $idEstudiante): array
    {
        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Curso',
                'notas.Nota'
            )
            ->where('EstudianteCarrera.IdUsuario', $idEstudiante)
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function materiasEstudiante(int $idEstudiante): array
    {
        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Curso',
                'inscripciones.Fecha'
            )
            ->where('EstudianteCarrera.IdUsuario', $idEstudiante)
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }
}
