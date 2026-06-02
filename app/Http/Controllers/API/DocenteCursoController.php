<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DocenteCursoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $idDocente = (int) $request->user()->IdUsuario;

            $cursos = \Illuminate\Support\Facades\DB::table('cursos_materias')
                ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
                ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
                ->join('turnos', 'cursos_materias.IdTurno', '=', 'turnos.IdTurno')
                ->where('cursos_materias.IdDocente', $idDocente)
                ->where('cursos_materias.Estado', true)
                ->select(
                    'cursos_materias.IdCursoMateria',
                    'cursos_materias.FechaInicio',
                    'cursos_materias.FechaFin',
                    'cursos_materias.MaxInscritos',
                    'cursos_materias.Inscritos',
                    'materias.Nombre as MateriaNombre',
                    'materias.CodigoMateria',
                    'cursos.Aula',
                    'cursos.Piso',
                    'cursos.Nombre as AulaNombre',
                    'turnos.Nombre as TurnoNombre',
                    'turnos.HoraInicio',
                    'turnos.HoraFin'
                )
                ->orderBy('materias.Nombre')
                ->get()
                ->map(fn($c) => [
                    'IdCursoMateria' => $c->IdCursoMateria,
                    'FechaInicio' => $c->FechaInicio,
                    'FechaFin' => $c->FechaFin,
                    'MaxInscritos' => (int) $c->MaxInscritos,
                    'Inscritos' => (int) $c->Inscritos,
                    'Materia' => [
                        'Nombre' => $c->MateriaNombre,
                        'CodigoMateria' => $c->CodigoMateria,
                    ],
                    'Curso' => [
                        'Aula' => $c->Aula,
                        'Piso' => $c->Piso,
                        'Nombre' => $c->AulaNombre ?? ('Aula ' . $c->Aula),
                    ],
                    'Turno' => [
                        'Nombre' => $c->TurnoNombre,
                        'HoraInicio' => $c->HoraInicio,
                        'HoraFin' => $c->HoraFin,
                    ],
                ])
                ->all();

            return response()->json([
                'status' => true,
                'data' => $cursos,
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function alumnos(Request $request, int $idCursoMateria): JsonResponse
    {
        try {
            $idDocente = (int) $request->user()->IdUsuario;

            $existe = \Illuminate\Support\Facades\DB::table('cursos_materias')
                ->where('IdCursoMateria', $idCursoMateria)
                ->where('IdDocente', $idDocente)
                ->exists();

            if (!$existe) {
                return response()->json([
                    'status' => false,
                    'message' => 'El curso especificado no está asignado a tu docencia.',
                    'data' => [],
                ], 403);
            }

            $alumnos = \Illuminate\Support\Facades\DB::table('inscripciones')
                ->join('EstudianteCarrera', 'inscripciones.IdEstudiante', '=', 'EstudianteCarrera.IdEstudianteCarrera')
                ->join('usuarios', 'EstudianteCarrera.IdUsuario', '=', 'usuarios.IdUsuario')
                ->leftJoin('notas', 'inscripciones.IdInscripcion', '=', 'notas.IdInscripcion')
                ->where('inscripciones.IdCursoMateria', $idCursoMateria)
                ->where('usuarios.Estado', true)
                ->select(
                    'usuarios.IdUsuario',
                    \Illuminate\Support\Facades\DB::raw("CONCAT(usuarios.Nombre1, ' ', COALESCE(usuarios.Nombre2, ''), ' ', usuarios.Apellido1, ' ', COALESCE(usuarios.Apellido2, '')) as Nombre"),
                    'usuarios.CI',
                    'usuarios.Correo',
                    'inscripciones.IdInscripcion',
                    'inscripciones.Fecha as FechaInscripcion',
                    'inscripciones.Aprobado',
                    'notas.Nota',
                    'notas.IdNota'
                )
                ->orderBy('usuarios.Apellido1')
                ->get()
                ->map(fn($a) => [
                    'IdUsuario' => $a->IdUsuario,
                    'Nombre' => trim($a->Nombre),
                    'CI' => $a->CI,
                    'Correo' => $a->Correo,
                    'IdInscripcion' => $a->IdInscripcion,
                    'FechaInscripcion' => $a->FechaInscripcion,
                    'Aprobado' => (bool) $a->Aprobado,
                    'Nota' => $a->Nota !== null ? round((float) $a->Nota, 2) : null,
                    'IdNota' => $a->IdNota,
                    'EstadoNota' => $a->Nota !== null,
                ])
                ->all();

            return response()->json([
                'status' => true,
                'data' => $alumnos,
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
