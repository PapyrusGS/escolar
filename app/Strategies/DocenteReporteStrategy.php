<?php

namespace App\Strategies;

use Illuminate\Support\Facades\DB;

class DocenteReporteStrategy implements ReporteStrategyInterface
{
    public function getTiposReportes(): array
    {
        return [
            'notas_alumno_cursos' => 'Notas por alumno de todos sus cursos',
            'notas_curso_alumnos' => 'Notas por curso de todos los alumnos',
            'alumnos_inscritos_curso' => 'Listado de alumnos inscritos a un curso',
            'materias_docente' => 'Listado de materias del docente',
        ];
    }

    public function getTitulo(string $tipoReporte): string
    {
        $titulos = [
            'notas_alumno_cursos' => 'Notas por Alumno de Todos sus Cursos',
            'notas_curso_alumnos' => 'Notas por Curso de Todos los Alumnos',
            'alumnos_inscritos_curso' => 'Alumnos Inscritos al Curso',
            'materias_docente' => 'Mis Materias Asignadas',
        ];
        return $titulos[$tipoReporte] ?? 'Reporte de Docente';
    }

    public function getFiltros(string $tipoReporte): array
    {
        $mapa = [
            'notas_alumno_cursos' => [
                ['tipo' => 'select', 'nombre' => 'IdEstudiante', 'label' => 'Estudiante', 'endpoint' => 'estudiantes_por_docente'],
            ],
            'notas_curso_alumnos' => [
                ['tipo' => 'select', 'nombre' => 'IdCurso', 'label' => 'Curso', 'endpoint' => 'cursos_por_docente'],
            ],
            'alumnos_inscritos_curso' => [
                ['tipo' => 'select', 'nombre' => 'IdCurso', 'label' => 'Curso', 'endpoint' => 'cursos_por_docente'],
            ],
            'materias_docente' => [],
        ];
        return $mapa[$tipoReporte] ?? [];
    }

    public function getDatos(string $tipoReporte, array $params): array
    {
        $idDocente = $params['IdUsuario'] ?? null;
        if (!$idDocente) {
            return [];
        }

        switch ($tipoReporte) {
            case 'notas_alumno_cursos':
                return $this->notasPorAlumno($params, $idDocente);
            case 'notas_curso_alumnos':
                return $this->notasPorCurso($params, $idDocente);
            case 'alumnos_inscritos_curso':
                return $this->alumnosInscritosCurso($params, $idDocente);
            case 'materias_docente':
                return $this->materiasDelDocente($idDocente);
            default:
                return [];
        }
    }

    private function notasPorAlumno(array $params, int $idDocente): array
    {
        if (empty($params['IdEstudiante'])) {
            return [];
        }

        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('usuarios as estudiantes', 'EstudianteCarrera.IdUsuario', '=', 'estudiantes.IdUsuario')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                DB::raw("CONCAT(estudiantes.Nombre1, ' ', estudiantes.Apellido1) as Estudiante"),
                'cursos.Nombre as Curso',
                'materias.Nombre as Materia',
                'notas.Nota'
            )
            ->where('cursos_materias.IdDocente', $idDocente)
            ->where('EstudianteCarrera.IdUsuario', $params['IdEstudiante'])
            ->orderBy('cursos.Nombre')
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function notasPorCurso(array $params, int $idDocente): array
    {
        if (empty($params['IdCurso'])) {
            return [];
        }

        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('usuarios as estudiantes', 'EstudianteCarrera.IdUsuario', '=', 'estudiantes.IdUsuario')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                'cursos.Nombre as Curso',
                DB::raw("CONCAT(estudiantes.Nombre1, ' ', estudiantes.Apellido1) as Estudiante"),
                'materias.Nombre as Materia',
                'notas.Nota'
            )
            ->where('cursos.IdCurso', $params['IdCurso'])
            ->where('cursos_materias.IdDocente', $idDocente)
            ->orderBy('estudiantes.Apellido1')
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function alumnosInscritosCurso(array $params, int $idDocente): array
    {
        if (empty($params['IdCurso'])) {
            return [];
        }

        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('usuarios as estudiantes', 'EstudianteCarrera.IdUsuario', '=', 'estudiantes.IdUsuario')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->select(
                'cursos.Nombre as Curso',
                'estudiantes.CI',
                DB::raw("CONCAT(estudiantes.Nombre1, ' ', estudiantes.Apellido1) as Estudiante"),
                'estudiantes.Correo',
                'inscripciones.Fecha'
            )
            ->where('cursos.IdCurso', $params['IdCurso'])
            ->where('cursos_materias.IdDocente', $idDocente)
            ->distinct()
            ->orderBy('estudiantes.Apellido1')
            ->get()->toArray();
    }

    private function materiasDelDocente(int $idDocente): array
    {
        return DB::table('cursos_materias')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'cursos.Nombre as Curso'
            )
            ->where('cursos_materias.IdDocente', $idDocente)
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }
}
