<?php

namespace App\Strategies;

use Illuminate\Support\Facades\DB;

class AdminReporteStrategy implements ReporteStrategyInterface
{
    public function getTiposReportes(): array
    {
        return [
            'materias_carrera' => 'Listado de materias por carrera',
            'notas_alumno_cursos' => 'Notas por alumno de todos sus cursos',
            'notas_curso_alumnos' => 'Notas por curso de todos los alumnos',
            'historial_alumno' => 'Historial académico del alumno',
            'notas_todas_materias' => 'Notas de todas materias',
            'alumnos_inscritos_curso' => 'Listado de alumnos inscritos a un curso',
            'materias_alumno' => 'Listado de materias del alumno',
            'materias_docente' => 'Listado de materias del docente',
            'info_alumno' => 'Información completa del alumno',
            'notas_curso_materia' => 'Notas de materia por curso',
        ];
    }

    public function getTitulo(string $tipoReporte): string
    {
        $titulos = [
            'materias_carrera' => 'Listado de Materias por Carrera',
            'notas_alumno_cursos' => 'Notas por Alumno de Todos sus Cursos',
            'notas_curso_alumnos' => 'Notas por Curso de Todos los Alumnos',
            'historial_alumno' => 'Historial Académico del Alumno',
            'notas_todas_materias' => 'Notas de Todas las Materias',
            'alumnos_inscritos_curso' => 'Listado de Alumnos Inscritos a un Curso',
            'materias_alumno' => 'Listado de Materias del Alumno',
            'materias_docente' => 'Listado de Materias del Docente',
            'info_alumno' => 'Información Completa del Alumno',
            'notas_curso_materia' => 'Notas de Materia por Curso',
        ];
        return $titulos[$tipoReporte] ?? 'Reporte de Administración';
    }

    public function getFiltros(string $tipoReporte): array
    {
        $comun = [
            ['tipo' => 'select', 'nombre' => 'IdCarrera', 'label' => 'Carrera', 'endpoint' => 'carreras'],
            ['tipo' => 'select', 'nombre' => 'IdCurso', 'label' => 'Curso', 'endpoint' => 'cursos'],
            ['tipo' => 'select', 'nombre' => 'IdMateria', 'label' => 'Materia', 'endpoint' => 'materias'],
            ['tipo' => 'select', 'nombre' => 'IdDocente', 'label' => 'Docente', 'endpoint' => 'docentes'],
            ['tipo' => 'select', 'nombre' => 'IdEstudiante', 'label' => 'Estudiante', 'endpoint' => 'estudiantes'],
        ];

        $mapa = [
            'materias_carrera' => [array_merge($comun[0], ['required' => false])],
            'notas_alumno_cursos' => [$comun[4]],
            'notas_curso_alumnos' => [$comun[1]],
            'historial_alumno' => [$comun[4]],
            'notas_todas_materias' => [$comun[4]],
            'alumnos_inscritos_curso' => [$comun[1]],
            'materias_alumno' => [$comun[4]],
            'materias_docente' => [$comun[3]],
            'info_alumno' => [$comun[4]],
            'notas_curso_materia' => [$comun[2]],
        ];

        return $mapa[$tipoReporte] ?? [];
    }

    public function getDatos(string $tipoReporte, array $params): array
    {
        switch ($tipoReporte) {
            case 'materias_carrera':
                return $this->materiasPorCarrera($params);
            case 'notas_alumno_cursos':
                return $this->notasPorAlumno($params);
            case 'notas_curso_alumnos':
                return $this->notasPorCurso($params);
            case 'historial_alumno':
                return $this->historialAlumno($params);
            case 'notas_todas_materias':
                return $this->notasTodasMaterias($params);
            case 'alumnos_inscritos_curso':
                return $this->alumnosInscritosCurso($params);
            case 'materias_alumno':
                return $this->materiasDelAlumno($params);
            case 'materias_docente':
                return $this->materiasDelDocente($params);
            case 'info_alumno':
                return $this->infoAlumno($params);
            case 'notas_curso_materia':
                return $this->notasCursoMateria($params);
            default:
                return [];
        }
    }

    private function materiasPorCarrera(array $params): array
    {
        $query = DB::table('carreras')
            ->join('carreraMateriaPensum', 'carreras.IdCarrera', '=', 'carreraMateriaPensum.IdCarrera')
            ->join('materias', 'carreraMateriaPensum.IdMateria', '=', 'materias.IdMateria')
            ->join('pensum', 'carreraMateriaPensum.IdPensum', '=', 'pensum.IdPensum')
            ->select(
                'carreras.Nombre as Carrera',
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'pensum.Nombre as Pensum',
                'carreraMateriaPensum.Semestre'
            )
            ->where('carreras.Estado', true)
            ->where('materias.Estado', true);

        if (!empty($params['IdCarrera'])) {
            $query->where('carreras.IdCarrera', $params['IdCarrera']);
        }

        return $query->orderBy('carreras.Nombre')
            ->orderBy('carreraMateriaPensum.Semestre')
            ->get()->toArray();
    }

    private function notasPorAlumno(array $params): array
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
                'notas.Nota',
                'notas.Estado as NotaEstado'
            )
            ->where('EstudianteCarrera.IdUsuario', $params['IdEstudiante'])
            ->orderBy('cursos.Nombre')
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function notasPorCurso(array $params): array
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
            ->join('usuarios as docentes', 'cursos_materias.IdDocente', '=', 'docentes.IdUsuario')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                'cursos.Nombre as Curso',
                DB::raw("CONCAT(estudiantes.Nombre1, ' ', estudiantes.Apellido1) as Estudiante"),
                'materias.Nombre as Materia',
                DB::raw("CONCAT(docentes.Nombre1, ' ', docentes.Apellido1) as Docente"),
                'notas.Nota'
            )
            ->where('cursos.IdCurso', $params['IdCurso'])
            ->orderBy('estudiantes.Apellido1')
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function historialAlumno(array $params): array
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
                'notas.Nota',
                'inscripciones.Aprobado',
                'inscripciones.Fecha'
            )
            ->where('EstudianteCarrera.IdUsuario', $params['IdEstudiante'])
            ->orderBy('inscripciones.Fecha', 'desc')
            ->get()->toArray();
    }

    private function notasTodasMaterias(array $params): array
    {
        if (empty($params['IdEstudiante'])) {
            return [];
        }

        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'cursos.Nombre as Curso',
                'notas.Nota'
            )
            ->where('EstudianteCarrera.IdUsuario', $params['IdEstudiante'])
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function alumnosInscritosCurso(array $params): array
    {
        if (empty($params['IdCurso'])) {
            return [];
        }

        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('usuarios as estudiantes', 'EstudianteCarrera.IdUsuario', '=', 'estudiantes.IdUsuario')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('usuarios as docentes', 'cursos_materias.IdDocente', '=', 'docentes.IdUsuario')
            ->select(
                'cursos.Nombre as Curso',
                'estudiantes.CI',
                DB::raw("CONCAT(estudiantes.Nombre1, ' ', estudiantes.Apellido1) as Estudiante"),
                'estudiantes.Correo',
                DB::raw("CONCAT(docentes.Nombre1, ' ', docentes.Apellido1) as DocenteAsignado")
            )
            ->where('cursos.IdCurso', $params['IdCurso'])
            ->distinct()
            ->orderBy('estudiantes.Apellido1')
            ->get()->toArray();
    }

    private function materiasDelAlumno(array $params): array
    {
        if (empty($params['IdEstudiante'])) {
            return [];
        }

        return DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'cursos.Nombre as Curso',
                'inscripciones.Fecha'
            )
            ->where('EstudianteCarrera.IdUsuario', $params['IdEstudiante'])
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function materiasDelDocente(array $params): array
    {
        if (empty($params['IdDocente'])) {
            return [];
        }

        return DB::table('cursos_materias')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'cursos.Nombre as Curso'
            )
            ->where('cursos_materias.IdDocente', $params['IdDocente'])
            ->orderBy('materias.Nombre')
            ->get()->toArray();
    }

    private function infoAlumno(array $params): array
    {
        if (empty($params['IdEstudiante'])) {
            return [];
        }

        $usuario = DB::table('usuarios')
            ->leftJoin('EstudianteCarrera', 'usuarios.IdUsuario', '=', 'EstudianteCarrera.IdUsuario')
            ->leftJoin('carreras', 'EstudianteCarrera.IdCarrera', '=', 'carreras.IdCarrera')
            ->leftJoin('modalidad', 'EstudianteCarrera.IdModalidad', '=', 'modalidad.IdModalidad')
            ->select(
                'usuarios.IdUsuario',
                'usuarios.Nombre1',
                'usuarios.Nombre2',
                'usuarios.Apellido1',
                'usuarios.Apellido2',
                'usuarios.CI',
                'usuarios.Telefono',
                'usuarios.Correo',
                'carreras.Nombre as Carrera',
                'modalidad.Nombre as Modalidad',
                'EstudianteCarrera.IdCarrera',
                'EstudianteCarrera.IdModalidad'
            )
            ->where('usuarios.IdUsuario', $params['IdEstudiante'])
            ->first();

        if (!$usuario) {
            return [];
        }

        $materias = DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->join('usuarios as docentes', 'cursos_materias.IdDocente', '=', 'docentes.IdUsuario')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'cursos.Nombre as Curso',
                DB::raw("CONCAT(docentes.Nombre1, ' ', docentes.Apellido1) as Docente"),
                'notas.Nota'
            )
            ->where('EstudianteCarrera.IdUsuario', $params['IdEstudiante'])
            ->orderBy('materias.Nombre')
            ->get()->toArray();

        return [
            'datos_personales' => $usuario,
            'materias' => $materias,
        ];
    }

    private function notasCursoMateria(array $params): array
    {
        if (empty($params['IdMateria'])) {
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
                'materias.Nombre as Materia',
                DB::raw("CONCAT(estudiantes.Nombre1, ' ', estudiantes.Apellido1) as Estudiante"),
                'estudiantes.CI',
                'notas.Nota'
            )
            ->where('cursos_materias.IdMateria', $params['IdMateria'])
            ->orderBy('cursos.Nombre')
            ->orderBy('estudiantes.Apellido1')
            ->get()->toArray();
    }
}
