<?php

namespace App\Strategies;

use Illuminate\Support\Facades\DB;

class AdminReporteStrategy implements ReporteStrategyInterface
{
    public function getTiposReportes(): array
    {
        return [
            'materias_carrera' => 'Listado de materias por carrera',
            'historial_alumno' => 'Historial completo del alumno',
            'historial_docente' => 'Historial completo del docente',
            'notas_todos_alumnos' => 'Notas de todos los alumnos por materia',
            'notas_por_fecha' => 'Notas por materia en rango de fechas',

        ];
    }

    public function getTitulo(string $tipoReporte): string
    {
        $titulos = [
            'materias_carrera' => 'Listado de Materias por Carrera',
            'historial_alumno' => 'Historial Completo del Alumno',
            'historial_docente' => 'Historial Completo del Docente',
            'notas_todos_alumnos' => 'Notas de Todos los Alumnos por Materia',
            'notas_por_fecha' => 'Notas por Materia en Rango de Fechas',

        ];
        return $titulos[$tipoReporte] ?? 'Reporte de Administración';
    }

    public function getFiltros(string $tipoReporte): array
    {
        $comun = [
            'carrera'   => ['tipo' => 'select', 'nombre' => 'IdCarrera', 'label' => 'Carrera', 'endpoint' => 'carreras'],
            'curso'     => ['tipo' => 'select', 'nombre' => 'IdCurso', 'label' => 'Curso', 'endpoint' => 'cursos'],
            'docente'   => ['tipo' => 'select', 'nombre' => 'IdDocente', 'label' => 'Docente', 'endpoint' => 'docentes'],
            'estudiante'=> ['tipo' => 'select', 'nombre' => 'IdEstudiante', 'label' => 'Estudiante', 'endpoint' => 'estudiantes'],
            'materia'   => ['tipo' => 'select', 'nombre' => 'IdMateria', 'label' => 'Materia', 'endpoint' => 'materias'],
            'fecha_desde'=> ['tipo' => 'date', 'nombre' => 'fecha_desde', 'label' => 'Fecha inicio'],
            'fecha_hasta'=> ['tipo' => 'date', 'nombre' => 'fecha_hasta', 'label' => 'Fecha fin'],
        ];

        $mapa = [
            'materias_carrera' => [array_merge($comun['carrera'], ['required' => false])],
            'historial_alumno' => [$comun['estudiante']],
            'historial_docente' => [$comun['docente']],
            'notas_todos_alumnos' => [$comun['materia']],
            'notas_por_fecha' => [$comun['materia'], $comun['fecha_desde'], $comun['fecha_hasta']],

        ];

        return $mapa[$tipoReporte] ?? [];
    }

    public function getDatos(string $tipoReporte, array $params): array
    {
        switch ($tipoReporte) {
            case 'materias_carrera':
                return $this->materiasPorCarrera($params);
            case 'historial_alumno':
                return $this->historialAlumno($params);
            case 'historial_docente':
                return $this->historialDocente($params);
            case 'notas_todos_alumnos':
                return $this->notasTodosAlumnos($params);
            case 'notas_por_fecha':
                return $this->notasPorFecha($params);

            default:
                return [];
        }
    }

    private function materiasPorCarrera(array $params): array
    {
        $codigoCarrera = [
            1 => 'SIS',
            2 => 'CON',
            3 => 'DG',
            4 => 'ADM',
            5 => 'DER',
        ];

        $nombreCarrera = [
            1 => 'Ingeniería de Sistemas',
            2 => 'Contaduría Pública',
            3 => 'Diseño Gráfico',
            4 => 'Administración de Empresas',
            5 => 'Derecho',
        ];

        $query = DB::table('materias')
            ->leftJoin('materias as previa', 'materias.IdMateriaPrevia', '=', 'previa.IdMateria')
            ->select(
                DB::raw("'' as Carrera"),
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'previa.Nombre as Prerrequisito',
                DB::raw("CONCAT('Semestre ', LEFT(SUBSTRING_INDEX(materias.CodigoMateria, '-', -1), 1)) as Semestre")
            )
            ->where('materias.Estado', true);

        if (!empty($params['IdCarrera'])) {
            $id = (int) $params['IdCarrera'];
            $prefix = $codigoCarrera[$id] ?? null;
            if ($prefix) {
                $query->where('materias.CodigoMateria', 'like', $prefix . '%');
                $query->addSelect(DB::raw("'" . $nombreCarrera[$id] . "' as Carrera"));
            } else {
                $query->addSelect(DB::raw("'Desconocida' as Carrera"));
            }
        } else {
            $query->addSelect(
                DB::raw("CASE
                    WHEN materias.CodigoMateria LIKE 'SIS%' THEN 'Ingeniería de Sistemas'
                    WHEN materias.CodigoMateria LIKE 'CON%' THEN 'Contaduría Pública'
                    WHEN materias.CodigoMateria LIKE 'DG%' THEN 'Diseño Gráfico'
                    WHEN materias.CodigoMateria LIKE 'ADM%' THEN 'Administración de Empresas'
                    WHEN materias.CodigoMateria LIKE 'DER%' THEN 'Derecho'
                    ELSE 'Otra'
                END as Carrera")
            );
        }

        return $query->orderBy('materias.CodigoMateria')
            ->get()->toArray();
    }

    private function historialAlumno(array $params): array
    {
        if (empty($params['IdEstudiante'])) {
            return [];
        }

        $usuario = DB::table('usuarios')
            ->leftJoin('EstudianteCarrera', 'usuarios.IdUsuario', '=', 'EstudianteCarrera.IdUsuario')
            ->leftJoin('carreras', 'EstudianteCarrera.IdCarrera', '=', 'carreras.IdCarrera')
            ->leftJoin('modalidad', 'EstudianteCarrera.IdModalidad', '=', 'modalidad.IdModalidad')
            ->select(
                'usuarios.Nombre1',
                'usuarios.Nombre2',
                'usuarios.Apellido1',
                'usuarios.Apellido2',
                'usuarios.CI',
                'usuarios.Telefono',
                'usuarios.Correo',
                'carreras.Nombre as Carrera',
                'modalidad.Nombre as Modalidad'
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
                'materias.Nombre as Curso',
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

    private function historialDocente(array $params): array
    {
        if (empty($params['IdDocente'])) {
            return [];
        }

        $usuario = DB::table('usuarios')
            ->select(
                'usuarios.Nombre1',
                'usuarios.Nombre2',
                'usuarios.Apellido1',
                'usuarios.Apellido2',
                'usuarios.CI',
                'usuarios.Telefono',
                'usuarios.Correo'
            )
            ->where('usuarios.IdUsuario', $params['IdDocente'])
            ->first();

        if (!$usuario) {
            return [];
        }

        $materias = DB::table('cursos_materias')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->join('turnos', 'cursos_materias.IdTurno', '=', 'turnos.IdTurno')
            ->select(
                'materias.CodigoMateria',
                'materias.Nombre as Curso',
                'cursos.Aula',
                'cursos.Piso',
                'turnos.Nombre as Turno',
                'cursos_materias.FechaInicio',
                'cursos_materias.FechaFin'
            )
            ->where('cursos_materias.IdDocente', $params['IdDocente'])
            ->orderBy('materias.Nombre')
            ->get()->toArray();

        return [
            'datos_personales' => $usuario,
            'materias' => $materias,
        ];
    }

    private function notasTodosAlumnos(array $params): array
    {
        $query = DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('usuarios as estudiantes', 'EstudianteCarrera.IdUsuario', '=', 'estudiantes.IdUsuario')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->join('usuarios as docentes', 'cursos_materias.IdDocente', '=', 'docentes.IdUsuario')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                DB::raw("CONCAT(cursos.Aula, ' - ', docentes.Nombre1, ' ', docentes.Apellido1) as Curso"),
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                DB::raw("CONCAT(estudiantes.Nombre1, ' ', estudiantes.Apellido1) as Estudiante"),
                DB::raw("CONCAT(docentes.Nombre1, ' ', docentes.Apellido1) as Docente"),
                'notas.Nota'
            );

        if (!empty($params['IdMateria'])) {
            $query->where('materias.IdMateria', $params['IdMateria']);
        }

        return $query->orderBy('cursos.Aula')
            ->orderBy('estudiantes.Apellido1')
            ->get()->toArray();
    }

    private function notasPorFecha(array $params): array
    {
        if (empty($params['IdMateria'])) {
            return [];
        }

        $query = DB::table('cursos_materias')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->join('usuarios as docentes', 'cursos_materias.IdDocente', '=', 'docentes.IdUsuario')
            ->join('turnos', 'cursos_materias.IdTurno', '=', 'turnos.IdTurno')
            ->leftJoin('inscripciones', 'cursos_materias.IdCursoMateria', '=', 'inscripciones.IdCursoMateria')
            ->leftJoin('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->leftJoin('usuarios as estudiantes', 'EstudianteCarrera.IdUsuario', '=', 'estudiantes.IdUsuario')
            ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
            ->select(
                DB::raw("CONCAT(cursos.Aula, ' - ', docentes.Nombre1, ' ', docentes.Apellido1) as Curso"),
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                DB::raw("CONCAT(estudiantes.Nombre1, ' ', estudiantes.Apellido1) as Estudiante"),
                DB::raw("CONCAT(docentes.Nombre1, ' ', docentes.Apellido1) as Docente"),
                'turnos.Nombre as Turno',
                'cursos_materias.FechaInicio',
                'cursos_materias.FechaFin',
                'inscripciones.Fecha as FechaInscripcion',
                'notas.Nota'
            )
            ->where('materias.IdMateria', $params['IdMateria'])
            ->orderBy('cursos.Aula')
            ->orderBy('estudiantes.Apellido1');

        if (!empty($params['fecha_desde'])) {
            $query->where('cursos_materias.FechaInicio', '>=', $params['fecha_desde']);
        }
        if (!empty($params['fecha_hasta'])) {
            $query->where('cursos_materias.FechaFin', '<=', $params['fecha_hasta']);
        }

        return $query->get()->toArray();
    }


}
