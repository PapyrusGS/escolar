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
            // Forzar idioma en español para las fechas legibles dinámicas (diffForHumans)
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
        $totalDocentes = User::where('IdRol', 2)->count();
        $totalMaterias = DB::table('materias')->count();
        $totalAulas = DB::table('cursos')->count();
        $totalCursosProgramados = DB::table('cursos_materias')->count();

        // Actividades Recientes: Últimos 5 usuarios registrados
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
                'fecha' => Carbon::parse($u->FechaRegistro)->diffForHumans(),
            ])
            ->all();

        // Actividades Recientes: Últimos 5 cursos programados
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
                'fecha' => Carbon::parse($c->FechaRegistro)->diffForHumans(),
            ])
            ->all();

        // Mezclar actividades cronológicamente
        $actividades = collect(array_merge($recentUsers, $recentCourses))
            ->sortByDesc('fecha')
            ->take(5)
            ->values()
            ->all();

        return [
            'rol' => 'Administrador',
            'resumen' => [
                ['titulo' => 'Estudiantes Activos', 'valor' => $totalEstudiantes, 'icono' => '🎓', 'gradiente' => 'linear-gradient(135deg, #34d399, #10b981)'],
                ['titulo' => 'Docentes Registrados', 'valor' => $totalDocentes, 'icono' => '💼', 'gradiente' => 'linear-gradient(135deg, #38bdf8, #2563eb)'],
                ['titulo' => 'Materias en Malla', 'valor' => $totalMaterias, 'icono' => '📘', 'gradiente' => 'linear-gradient(135deg, #f59e0b, #d97706)'],
                ['titulo' => 'Cursos Programados', 'valor' => $totalCursosProgramados, 'icono' => '🗓️', 'gradiente' => 'linear-gradient(135deg, #a78bfa, #7c3aed)'],
                ['titulo' => 'Aulas Físicas', 'valor' => $totalAulas, 'icono' => '🚪', 'gradiente' => 'linear-gradient(135deg, #f472b6, #db2777)'],
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
        $courses = DB::table('cursos_materias')
            ->where('IdDocente', $idUsuario)
            ->get();

        $totalCursos = $courses->count();
        $totalInscritos = $courses->sum('Inscritos');
        $capacidadMaxima = $courses->sum('MaxInscritos');

        // Actividades Recientes: Últimos 5 alumnos inscritos en sus clases
        $recentEnrollments = DB::table('inscripciones')
            ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
            ->join('usuarios', 'EstudianteCarrera.IdUsuario', '=', 'usuarios.IdUsuario')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->where('cursos_materias.IdDocente', $idUsuario)
            ->select(
                'usuarios.Nombre1',
                'usuarios.Apellido1',
                'materias.Nombre as Materia',
                'inscripciones.Fecha'
            )
            ->orderBy('inscripciones.IdInscripcion', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'tipo' => 'inscripcion',
                'titulo' => 'Alumno Inscrito',
                'descripcion' => "{$e->Nombre1} {$e->Apellido1} se inscribió en tu curso de {$e->Materia}.",
                'fecha' => Carbon::parse($e->Fecha)->diffForHumans(),
            ])
            ->all();

        return [
            'rol' => 'Docente',
            'resumen' => [
                ['titulo' => 'Materias a Cargo', 'valor' => $totalCursos, 'icono' => '📘', 'gradiente' => 'linear-gradient(135deg, #38bdf8, #2563eb)'],
                ['titulo' => 'Total Alumnos', 'valor' => $totalInscritos, 'icono' => '👥', 'gradiente' => 'linear-gradient(135deg, #34d399, #10b981)'],
                ['titulo' => 'Capacidad Máxima', 'valor' => $capacidadMaxima, 'icono' => '📊', 'gradiente' => 'linear-gradient(135deg, #a78bfa, #7c3aed)'],
            ],
            'actividades' => $recentEnrollments,
            'info_relevante' => [
                'mensaje' => 'Mantén al día tus programaciones horarias y supervisa las listas de alumnos matriculados en tus respectivas cátedras.',
                'ayuda' => 'Puedes consultar en tiempo real las fichas académicas y nóminas de inscritos en cada materia desde el visualizador de cursos.',
            ],
        ];
    }

    private function getEstudianteStats(int $idUsuario): array
    {
        // 1. Obtener la carrera del estudiante acoplada a su modalidad real de la BD
        $studentCareer = DB::table('EstudianteCarrera')
            ->join('modalidad', 'EstudianteCarrera.IdModalidad', '=', 'modalidad.IdModalidad')
            ->where('EstudianteCarrera.IdUsuario', $idUsuario)
            ->where('EstudianteCarrera.Estado', 1)
            ->select('EstudianteCarrera.IdEstudianteCarrera', 'modalidad.Nombre as Modalidad')
            ->first();

        $totalInscritos = 0;
        $totalAprobados = 0;
        $totalCursando = 0;
        $progresoPorcentaje = 0;
        $recentEnrollments = [];
        $nombreModalidad = 'No Asignada';

        if ($studentCareer) {
            $nombreModalidad = $studentCareer->Modalidad;
            $hoy = now()->toDateString();

            // Total de Inscripciones del estudiante
            $enrollments = DB::table('inscripciones')
                ->where('IdEstudiante', $studentCareer->IdEstudianteCarrera)
                ->where('Estado', 1)
                ->get();

            $totalInscritos = $enrollments->count();
            
            // Total de Materias Aprobadas (Buscando registros con bandera Aprobado = 1)
            $totalAprobados = $enrollments->where('Aprobado', true)->count();

            // 2. Extraer las materias que el alumno está cursando basándonos estrictamente en el Calendario Académico hoy
            $materiasVigentes = DB::table('inscripciones as i')
                ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
                ->where('i.IdEstudiante', $studentCareer->IdEstudianteCarrera)
                ->where('i.Estado', 1)
                ->where('cm.Estado', 1)
                ->where('cm.FechaInicio', '<=', $hoy)
                ->where('cm.FechaFin', '>=', $hoy)
                ->select('cm.FechaInicio', 'cm.FechaFin')
                ->get();

            $totalCursando = $materiasVigentes->count();

            // 3. Cálculo dinámico del progreso del periodo (Semestre o Módulo Mensual)
            if ($totalCursando > 0) {
                $fechaInicio = Carbon::parse($materiasVigentes[0]->FechaInicio);
                $fechaFin = Carbon::parse($materiasVigentes[0]->FechaFin);
                $ahora = Carbon::now();

                if ($ahora->greaterThanOrEqualTo($fechaFin)) {
                    $progresoPorcentaje = 100;
                } elseif ($ahora->lessThan($fechaInicio)) {
                    $progresoPorcentaje = 0;
                } else {
                    $totalDias = $fechaInicio->diffInDays($fechaFin);
                    $diasTranscurridos = $fechaInicio->diffInDays($ahora);
                    $progresoPorcentaje = $totalDias > 0 ? round(($diasTranscurridos / $totalDias) * 100) : 0;
                }
            }

            // Actividades Recientes: Últimas 5 inscripciones del alumno
            $recentEnrollments = DB::table('inscripciones')
                ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
                ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
                ->where('inscripciones.IdEstudiante', $studentCareer->IdEstudianteCarrera)
                ->where('inscripciones.Estado', 1)
                ->select(
                    'materias.Nombre as Materia',
                    'inscripciones.Fecha',
                    'inscripciones.Aprobado'
                )
                ->orderBy('inscripciones.IdInscripcion', 'desc')
                ->limit(5)
                ->get()
                ->map(fn($e) => [
                    'tipo' => 'inscripcion_estudiante',
                    'titulo' => 'Inscripción Exitosa',
                    'descripcion' => "Te inscribiste correctamente en la materia de {$e->Materia}." . ($e->Aprobado ? " (Estado: Aprobada)" : " (Estado: Cursando)"),
                    'fecha' => Carbon::parse($e->Fecha)->diffForHumans(),
                ])
                ->all();
        }

        // Si no se encuentran actividades del estudiante, inicializamos un hito base de bienvenida
        if (empty($recentEnrollments)) {
            $recentEnrollments[] = [
                'tipo' => 'sistema',
                'titulo' => 'Portal Estudiantil Activo',
                'descripcion' => 'Tu perfil de usuario está sincronizado y listo para el periodo actual.',
                'fecha' => 'Reciente'
            ];
        }

        return [
            'rol' => 'Estudiante',
            'resumen' => [
                ['titulo' => 'Materias Inscritas', 'valor' => $totalInscritos, 'icono' => '📘', 'gradiente' => 'linear-gradient(135deg, #38bdf8, #2563eb)'],
                ['titulo' => 'Materias Cursando', 'valor' => $totalCursando, 'icono' => '⏳', 'gradiente' => 'linear-gradient(135deg, #f59e0b, #d97706)'],
                ['titulo' => 'Materias Aprobadas', 'valor' => $totalAprobados, 'icono' => '🏆', 'gradiente' => 'linear-gradient(135deg, #34d399, #10b981)'],
            ],
            'actividades' => $recentEnrollments,
            'info_relevante' => [
                'mensaje' => "Estás cursando tu periodo académico actual bajo la modalidad: {$nombreModalidad}.",
                'ayuda' => "Progreso transcurrido del periodo lectivo actual: {$progresoPorcentaje}%",
                'progreso_porcentaje' => (int) $progresoPorcentaje // Llave inyectada para alimentar la barra en Dashboard.vue
            ],
        ];
    }
}