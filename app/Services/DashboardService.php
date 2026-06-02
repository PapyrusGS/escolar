<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use RuntimeException;

class DashboardService
{
    public function getStats(int $idUsuario, int $idRol): array
    {
        try {
            Carbon::setLocale('es');

            switch ($idRol) {
                case 1:
                    return $this->getAdminStats();
                case 2:
                    return $this->getDocenteStats($idUsuario);
                case 3:
                    return $this->getEstudianteStats($idUsuario);
                default:
                    throw new RuntimeException('Rol de usuario no soportado en el Dashboard.');
            }
        } catch (\Throwable $e) {
            throw new RuntimeException('Error al compilar estadísticas de panel: ' . $e->getMessage(), 0, $e);
        }
    }

    private function getAdminStats(): array
    {
        $totalEstudiantes = User::where('IdRol', 3)->count();
        $totalEstudiantesActivos = User::where('IdRol', 3)->where('Estado', true)->count();
        $totalDocentes = User::where('IdRol', 2)->count();
        $totalDocentesActivos = User::where('IdRol', 2)->where('Estado', true)->count();
        $totalMaterias = DB::table('materias')->where('Estado', true)->count();
        $totalAulas = DB::table('cursos')->where('Estado', true)->count();
        $totalCursosProgramados = DB::table('cursos_materias')->where('Estado', true)->count();
        $totalInscripcionesRecientes = DB::table('inscripciones')
            ->where('Estado', 1)
            ->where('Fecha', '>=', Carbon::now()->subDays(30))
            ->count();

        $recentUsers = DB::table('usuarios')
            ->join('roles', 'usuarios.IdRol', '=', 'roles.IdRol')
            ->select(
                'usuarios.Nombre1',
                'usuarios.Apellido1',
                'usuarios.CI',
                'usuarios.FechaRegistro',
                'roles.Nombre as Rol'
            )
            ->orderBy('usuarios.IdUsuario', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($u) => [
                'tipo' => 'usuario',
                'titulo' => 'Nuevo Usuario Registrado',
                'descripcion' => "{$u->Nombre1} {$u->Apellido1} (CI: {$u->CI}) fue registrado como {$u->Rol}.",
                'fecha' => $u->FechaRegistro ? Carbon::parse($u->FechaRegistro)->diffForHumans() : 'Reciente',
                'Fecha' => $u->FechaRegistro,
            ])
            ->all();

        $recentCourses = DB::table('cursos_materias')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->select(
                'materias.Nombre as Materia',
                'materias.CodigoMateria',
                'cursos.Aula',
                'cursos_materias.FechaRegistro'
            )
            ->orderBy('cursos_materias.IdCursoMateria', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($c) => [
                'tipo' => 'curso',
                'titulo' => 'Nueva Materia Programada',
                'descripcion' => "Se programó la materia {$c->Materia} ({$c->CodigoMateria}) en el Aula {$c->Aula}.",
                'fecha' => $c->FechaRegistro ? Carbon::parse($c->FechaRegistro)->diffForHumans() : 'Reciente',
                'Fecha' => $c->FechaRegistro,
            ])
            ->all();

        $recentEnrollments = DB::table('inscripciones as i')
            ->join('EstudianteCarrera as ec', 'i.IdEstudiante', '=', 'ec.IdEstudianteCarrera')
            ->join('usuarios as u', 'ec.IdUsuario', '=', 'u.IdUsuario')
            ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
            ->join('materias as m', 'cm.IdMateria', '=', 'm.IdMateria')
            ->select('u.Nombre1', 'u.Apellido1', 'm.Nombre as Materia', 'i.Fecha')
            ->orderBy('i.IdInscripcion', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'tipo' => 'inscripcion',
                'titulo' => 'Nueva Inscripción',
                'descripcion' => "{$e->Nombre1} {$e->Apellido1} se inscribió en {$e->Materia}.",
                'fecha' => $e->Fecha ? Carbon::parse($e->Fecha)->diffForHumans() : 'Reciente',
                'Fecha' => $e->Fecha,
            ])
            ->all();

        $actividades = collect(array_merge($recentUsers, $recentCourses, $recentEnrollments))
            ->sortByDesc('Fecha')
            ->take(8)
            ->values()
            ->all();

        return [
            'rol' => 'Administrador',
            'resumen' => [
                ['titulo' => 'Usuarios Registrados', 'valor' => (int) User::count(), 'variant' => 'primary'],
                ['titulo' => 'Estudiantes Activos', 'valor' => (int) $totalEstudiantesActivos, 'variant' => 'success'],
                ['titulo' => 'Docentes Activos', 'valor' => (int) $totalDocentesActivos, 'variant' => 'info'],
                ['titulo' => 'Materias Activas', 'valor' => (int) $totalMaterias, 'variant' => 'warning'],
                ['titulo' => 'Cursos Activos', 'valor' => (int) $totalCursosProgramados, 'variant' => 'student'],
                ['titulo' => 'Inscripciones Recientes', 'valor' => (int) $totalInscripcionesRecientes, 'variant' => 'teacher'],
            ],
            'totales' => [
                'estudiantes' => (int) $totalEstudiantes,
                'docentes' => (int) $totalDocentes,
                'materias' => (int) $totalMaterias,
                'aulas' => (int) $totalAulas,
                'cursos' => (int) $totalCursosProgramados,
                'inscripcionesRecientes' => (int) $totalInscripcionesRecientes,
            ],
            'actividades' => $actividades,
            'info_relevante' => [
                'mensaje' => 'Como Administrador tienes control total sobre la carga académica, vigencia de periodos escolares e inscripciones activas.',
                'ayuda' => 'Recuerda que la desactivación lógica de un curso escolar con inscripciones activas conserva el historial e impide fallos relacionales.',
            ],
        ];
    }

    private function getDocenteStats(int $idUsuario): array
    {
        $hoy = Carbon::now()->toDateString();

        $courses = DB::table('cursos_materias as cm')
            ->join('materias as m', 'cm.IdMateria', '=', 'm.IdMateria')
            ->join('cursos as c', 'cm.IdCurso', '=', 'c.IdCurso')
            ->join('turnos as t', 'cm.IdTurno', '=', 't.IdTurno')
            ->where('cm.IdDocente', $idUsuario)
            ->where('cm.Estado', true)
            ->select(
                'cm.IdCursoMateria',
                'cm.FechaInicio',
                'cm.FechaFin',
                'cm.Inscritos',
                'cm.MaxInscritos',
                'm.Nombre as MateriaNombre',
                'm.CodigoMateria',
                'c.Nombre as AulaNombre',
                'c.Aula',
                'c.Piso',
                't.Nombre as TurnoNombre',
                't.HoraInicio',
                't.HoraFin'
            )
            ->get();

        $totalCursos = $courses->count();
        $totalInscritos = (int) $courses->sum('Inscritos');
        $capacidadMaxima = (int) $courses->sum('MaxInscritos');

        $materiasAsignadas = $courses->map(function ($c) use ($hoy) {
            $fechaInicio = Carbon::parse($c->FechaInicio);
            $fechaFin = Carbon::parse($c->FechaFin);
            $ahora = Carbon::now();
            $estado = 'Próxima';
            $progreso = 0;

            if ($ahora->greaterThanOrEqualTo($fechaFin)) {
                $estado = 'Finalizada';
                $progreso = 100;
            } elseif ($ahora->lessThan($fechaInicio)) {
                $estado = 'Próxima';
                $progreso = 0;
            } else {
                $estado = 'En curso';
                $totalDias = $fechaInicio->diffInDays($fechaFin) ?: 1;
                $diasTranscurridos = $fechaInicio->diffInDays($ahora);
                $progreso = (int) round(($diasTranscurridos / $totalDias) * 100);
            }

            return [
                'IdCursoMateria' => $c->IdCursoMateria,
                'Materia' => $c->MateriaNombre,
                'CodigoMateria' => $c->CodigoMateria,
                'Aula' => $c->AulaNombre ?? ('Aula ' . $c->Aula),
                'Piso' => $c->Piso,
                'Turno' => $c->TurnoNombre,
                'HoraInicio' => $c->HoraInicio,
                'HoraFin' => $c->HoraFin,
                'FechaInicio' => $c->FechaInicio,
                'FechaFin' => $c->FechaFin,
                'Inscritos' => (int) $c->Inscritos,
                'MaxInscritos' => (int) $c->MaxInscritos,
                'Estado' => $estado,
                'Progreso' => $progreso,
            ];
        })->values()->all();

        $cursosActivos = collect($materiasAsignadas)
            ->filter(fn($m) => $m['Estado'] === 'En curso')
            ->count();

        $estudiantesUnicos = DB::table('inscripciones as i')
            ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
            ->where('cm.IdDocente', $idUsuario)
            ->where('i.Estado', 1)
            ->distinct()
            ->join('EstudianteCarrera as ec', 'i.IdEstudiante', '=', 'ec.IdEstudianteCarrera')
            ->distinct()
            ->count('ec.IdUsuario');

        $recentEnrollments = DB::table('inscripciones as i')
            ->join('EstudianteCarrera as ec', 'i.IdEstudiante', '=', 'ec.IdEstudianteCarrera')
            ->join('usuarios as u', 'ec.IdUsuario', '=', 'u.IdUsuario')
            ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
            ->join('materias as m', 'cm.IdMateria', '=', 'm.IdMateria')
            ->where('cm.IdDocente', $idUsuario)
            ->select('u.Nombre1', 'u.Apellido1', 'm.Nombre as Materia', 'i.Fecha')
            ->orderBy('i.IdInscripcion', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'tipo' => 'inscripcion',
                'titulo' => 'Alumno Inscrito',
                'descripcion' => "{$e->Nombre1} {$e->Apellido1} se inscribió en tu curso de {$e->Materia}.",
                'fecha' => $e->Fecha ? Carbon::parse($e->Fecha)->diffForHumans() : 'Reciente',
                'Fecha' => $e->Fecha,
            ])
            ->all();

        return [
            'rol' => 'Docente',
            'resumen' => [
                ['titulo' => 'Materias Asignadas', 'valor' => (int) $totalCursos, 'variant' => 'primary'],
                ['titulo' => 'Cursos Activos', 'valor' => (int) $cursosActivos, 'variant' => 'success'],
                ['titulo' => 'Estudiantes Inscritos', 'valor' => (int) $totalInscritos, 'variant' => 'info'],
                ['titulo' => 'Estudiantes Únicos', 'valor' => (int) $estudiantesUnicos, 'variant' => 'warning'],
            ],
            'materiasAsignadas' => $materiasAsignadas,
            'actividades' => $recentEnrollments,
            'info_relevante' => [
                'mensaje' => 'Mantén al día tus programaciones horarias y supervisa las listas de alumnos matriculados en tus respectivas cátedras.',
                'ayuda' => 'Puedes consultar en tiempo real las fichas académicas y nóminas de inscritos en cada materia desde el visualizador de cursos.',
            ],
        ];
    }

    private function getEstudianteStats(int $idUsuario): array
    {
        $hoy = Carbon::now()->toDateString();

        $studentCareer = DB::table('EstudianteCarrera as ec')
            ->join('carreras as ca', 'ec.IdCarrera', '=', 'ca.IdCarrera')
            ->join('modalidad as mo', 'ec.IdModalidad', '=', 'mo.IdModalidad')
            ->where('ec.IdUsuario', $idUsuario)
            ->where('ec.Estado', 1)
            ->select(
                'ec.IdEstudianteCarrera',
                'ec.IdCarrera',
                'ec.IdModalidad',
                'ca.Nombre as Carrera',
                'mo.Nombre as Modalidad',
                'mo.DuracionSemanasxMaterias',
                'mo.MaxMaterias'
            )
            ->first();

        $nombreCarrera = $studentCareer->Carrera ?? 'No asignada';
        $nombreModalidad = $studentCareer->Modalidad ?? 'No asignada';
        $idModalidad = (int) ($studentCareer->IdModalidad ?? 0);
        $idEstudianteCarrera = (int) ($studentCareer->IdEstudianteCarrera ?? 0);

        $esModular = $this->esModalidadModular($idModalidad, $nombreModalidad);

        $materiasInscritas = [];
        $totalInscritos = 0;
        $totalCursando = 0;
        $totalAprobados = 0;
        $progresoPorcentaje = 0;
        $estadoPeriodo = 'Sin materias activas';
        $recentEnrollments = [];
        $fechaInicioPeriodo = null;
        $fechaFinPeriodo = null;

        if ($studentCareer && $idEstudianteCarrera > 0) {
            $materiasVigentesRaw = DB::table('inscripciones as i')
                ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
                ->join('materias as m', 'cm.IdMateria', '=', 'm.IdMateria')
                ->join('cursos as c', 'cm.IdCurso', '=', 'c.IdCurso')
                ->join('turnos as t', 'cm.IdTurno', '=', 't.IdTurno')
                ->join('usuarios as d', 'cm.IdDocente', '=', 'd.IdUsuario')
                ->leftJoin('notas as n', function ($join) {
                    $join->on('n.IdInscripcion', '=', 'i.IdInscripcion')
                        ->where('n.Estado', 1);
                })
                ->where('i.IdEstudiante', $idEstudianteCarrera)
                ->where('i.Estado', 1)
                ->where('cm.Estado', 1)
                ->select(
                    'cm.IdCursoMateria',
                    'cm.FechaInicio',
                    'cm.FechaFin',
                    'm.Nombre as Materia',
                    'm.CodigoMateria',
                    'c.Nombre as AulaNombre',
                    'c.Aula',
                    'c.Piso',
                    't.Nombre as Turno',
                    't.HoraInicio',
                    't.HoraFin',
                    DB::raw("CONCAT(d.Nombre1, ' ', d.Apellido1) as Docente"),
                    'd.Correo as DocenteCorreo',
                    'i.Aprobado',
                    'i.Fecha as FechaInscripcion',
                    'n.Nota'
                )
                ->get();

            $totalInscritos = $materiasVigentesRaw->count();
            $totalAprobados = $materiasVigentesRaw->where('Aprobado', true)->count();

            $materiasActivas = $materiasVigentesRaw->filter(function ($m) use ($hoy) {
                return $m->FechaInicio <= $hoy && $m->FechaFin >= $hoy;
            });

            $totalCursando = $materiasActivas->count();

            if ($esModular && $totalCursando > 0) {
                $m = $materiasActivas->first();
                $fechaInicioPeriodo = $m->FechaInicio;
                $fechaFinPeriodo = $m->FechaFin;
            } elseif (! $esModular && $totalCursando > 0) {
                $fechaInicioPeriodo = $materiasActivas->min('FechaInicio');
                $fechaFinPeriodo = $materiasActivas->max('FechaFin');
            }

            if ($fechaInicioPeriodo && $fechaFinPeriodo) {
                $inicio = Carbon::parse($fechaInicioPeriodo);
                $fin = Carbon::parse($fechaFinPeriodo);
                $ahora = Carbon::now();

                if ($ahora->greaterThanOrEqualTo($fin)) {
                    $estadoPeriodo = 'Finalizado';
                    $progresoPorcentaje = 100;
                } elseif ($ahora->lessThan($inicio)) {
                    $estadoPeriodo = 'Próximo';
                    $progresoPorcentaje = 0;
                } else {
                    $estadoPeriodo = $esModular ? 'En curso' : 'En curso';
                    $totalDias = $inicio->diffInDays($fin) ?: 1;
                    $diasTranscurridos = $inicio->diffInDays($ahora);
                    $progresoPorcentaje = (int) round(($diasTranscurridos / $totalDias) * 100);
                }
            }

            $materiasInscritas = $materiasVigentesRaw->map(function ($m) use ($hoy) {
                $fechaInicio = Carbon::parse($m->FechaInicio);
                $fechaFin = Carbon::parse($m->FechaFin);
                $ahora = Carbon::now();

                if ($ahora->greaterThanOrEqualTo($fechaFin)) {
                    $estado = 'Finalizada';
                } elseif ($ahora->lessThan($fechaInicio)) {
                    $estado = 'Próxima';
                } else {
                    $estado = 'En curso';
                }

                return [
                    'IdCursoMateria' => $m->IdCursoMateria,
                    'Materia' => $m->Materia,
                    'CodigoMateria' => $m->CodigoMateria,
                    'Docente' => $m->Docente,
                    'DocenteCorreo' => $m->DocenteCorreo,
                    'Aula' => $m->AulaNombre ?? ('Aula ' . $m->Aula),
                    'Piso' => $m->Piso,
                    'Turno' => $m->Turno,
                    'HoraInicio' => $m->HoraInicio,
                    'HoraFin' => $m->HoraFin,
                    'FechaInicio' => $m->FechaInicio,
                    'FechaFin' => $m->FechaFin,
                    'FechaInscripcion' => $m->FechaInscripcion,
                    'Estado' => $estado,
                    'Nota' => $m->Nota,
                    'Aprobado' => (bool) $m->Aprobado,
                ];
            })->values()->all();

            $recentEnrollments = DB::table('inscripciones as i')
                ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
                ->join('materias as m', 'cm.IdMateria', '=', 'm.IdMateria')
                ->where('i.IdEstudiante', $idEstudianteCarrera)
                ->where('i.Estado', 1)
                ->select('m.Nombre as Materia', 'i.Fecha', 'i.Aprobado')
                ->orderBy('i.IdInscripcion', 'desc')
                ->limit(5)
                ->get()
                ->map(fn($e) => [
                    'tipo' => 'inscripcion_estudiante',
                    'titulo' => 'Inscripción a materia',
                    'descripcion' => "Te inscribiste en la materia {$e->Materia}." . ($e->Aprobado ? " (Aprobada)" : " (En curso)"),
                    'fecha' => $e->Fecha ? Carbon::parse($e->Fecha)->diffForHumans() : 'Reciente',
                    'Fecha' => $e->Fecha,
                ])
                ->all();
        }

        if (empty($recentEnrollments)) {
            $recentEnrollments[] = [
                'tipo' => 'sistema',
                'titulo' => 'Portal Estudiantil Activo',
                'descripcion' => 'Tu perfil de usuario está sincronizado y listo para el periodo actual.',
                'fecha' => 'Reciente',
                'Fecha' => Carbon::now(),
            ];
        }

        $user = User::find($idUsuario);
        $nombreCompleto = $user ? trim(($user->Nombre1 ?? '') . ' ' . ($user->Apellido1 ?? '')) : 'Estudiante';

        return [
            'rol' => 'Estudiante',
            'nombre' => $nombreCompleto,
            'carrera' => $nombreCarrera,
            'modalidad' => $nombreModalidad,
            'modalidadTipo' => $esModular ? 'Modular' : 'Semestral',
            'semestre' => $user?->Semestre ?? null,
            'resumen' => [
                ['titulo' => 'Materias Inscritas', 'valor' => (int) $totalInscritos, 'variant' => 'primary'],
                ['titulo' => 'Materias en Curso', 'valor' => (int) $totalCursando, 'variant' => 'info'],
                ['titulo' => 'Materias Aprobadas', 'valor' => (int) $totalAprobados, 'variant' => 'success'],
            ],
            'materiasInscritas' => $materiasInscritas,
            'materiasActivas' => array_values(array_filter($materiasInscritas, fn($m) => $m['Estado'] === 'En curso')),
            'progreso' => [
                'porcentaje' => (int) $progresoPorcentaje,
                'estado' => $estadoPeriodo,
                'fechaInicio' => $fechaInicioPeriodo,
                'fechaFin' => $fechaFinPeriodo,
            ],
            'actividades' => $recentEnrollments,
            'info_relevante' => [
                'mensaje' => "Estás cursando tu periodo académico actual bajo la modalidad: {$nombreModalidad}.",
                'ayuda' => "Progreso del periodo lectivo: {$progresoPorcentaje}% ({$estadoPeriodo})",
            ],
        ];
    }

    private function esModalidadModular(int $idModalidad, string $nombreModalidad): bool
    {
        if ($idModalidad > 0) {
            $modalidad = DB::table('modalidad')
                ->where('IdModalidad', $idModalidad)
                ->first();
            if ($modalidad) {
                $n = strtolower($modalidad->Nombre);
                if (str_contains($n, 'modul')) return true;
                if (str_contains($n, 'semestr')) return false;
            }
        }
        $n = strtolower($nombreModalidad);
        if (str_contains($n, 'modul')) return true;
        return false;
    }
}
