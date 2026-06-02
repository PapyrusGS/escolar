<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class EstudianteRepository
{
    /**
     * RF03 - Busca los datos del estudiante cruzando con su rol y modalidad
     */
    public function findUsuarioConRol(int $idUsuario)
    {
        return DB::table('usuarios')
            ->join('roles', 'usuarios.IdRol', '=', 'roles.IdRol')
            ->join('estudiantecarrera', 'usuarios.IdUsuario', '=', 'estudiantecarrera.IdUsuario')
            ->join('modalidad', 'estudiantecarrera.IdModalidad', '=', 'modalidad.IdModalidad')
            ->where('usuarios.IdUsuario', $idUsuario)
            ->select(
                'usuarios.IdUsuario',
                'usuarios.Nombre1',
                'usuarios.Nombre2',
                'usuarios.Apellido1',
                'usuarios.Apellido2',
                'usuarios.Correo',
                'usuarios.CI',
                'usuarios.Telefono',
                'roles.Nombre as Rol',
                'modalidad.Nombre as Modalidad'
            )
            ->first();
    }

    /**
     * RF03 - Actualiza cualquier campo en la tabla usuarios de forma dinámica
     */
    public function updateUsuario(int $idUsuario, array $data)
    {
        DB::table('usuarios')
            ->where('IdUsuario', $idUsuario)
            ->update($data);
        
        return DB::table('usuarios')
            ->where('IdUsuario', $idUsuario)
            ->select('IdUsuario', 'Nombre1', 'Nombre2', 'Apellido1', 'Apellido2', 'Correo', 'Telefono', 'CI')
            ->first();
    }

    // =========================================================================
    // NUEVOS MÉTODOS REESTRUCTURADOS PARA EL RF05 (INSCRIPCIÓN A MATERIAS)
    // =========================================================================

    /**
     * RF05 - Obtiene el registro académico del estudiante activo
     */
    public function getModalidadEstudiante(int $idUsuario)
    {
        return DB::table('estudiantecarrera')
            ->where('IdUsuario', $idUsuario)
            ->where('Estado', 1)
            ->select('IdEstudianteCarrera', 'IdCarrera', 'IdModalidad')
            ->first();
    }

    /**
     * RF05 - Muestra las materias disponibles que corresponden al Pensum, la Modalidad 
     * del alumno y además valida que tenga los prerrequisitos aprobados.
     */
    public function getMateriasPorModalidad(int $idUsuario, int $idModalidad)
    {
        // 1. Buscamos el registro académico del alumno en la tabla estudiantecarrera
        $estudianteCarrera = $this->getModalidadEstudiante($idUsuario);

        if (!$estudianteCarrera) {
            return [];
        }

        // 2. Obtenemos las combinaciones de IdCursoMateria en las que el alumno ya está inscrito
        $inscritasIds = DB::table('inscripciones')
            ->where('IdEstudiante', $estudianteCarrera->IdEstudianteCarrera)
            ->where('Estado', 1)
            ->pluck('IdCursoMateria');

        // 3. Traemos los IDs de las materias que pertenecen al Pensum asociado a esa Modalidad y Carrera
        $materiasPensumIds = DB::table('carreramateriapensum')
            ->join('pensum', 'carreramateriapensum.IdPensum', '=', 'pensum.IdPensum')
            ->where('carreramateriapensum.IdCarrera', $estudianteCarrera->IdCarrera)
            ->where('pensum.IdModalidad', $idModalidad) // Filtro por la modalidad asignada (Semestral / Modular)
            ->where('carreramateriapensum.Estado', 1)
            ->pluck('carreramateriapensum.IdMateria');

        // 4. Listamos los cursos_materias disponibles haciendo los cruces limpios y aplicando el escudo de prerrequisitos
        return DB::table('cursos_materias')
            ->join('cursos', 'cursos_materias.IdCurso', '=', 'cursos.IdCurso')
            ->join('materias', 'cursos_materias.IdMateria', '=', 'materias.IdMateria')
            ->join('turnos', 'cursos_materias.IdTurno', '=', 'turnos.IdTurno')
            ->whereNotIn('cursos_materias.IdCursoMateria', $inscritasIds)
            ->whereIn('cursos_materias.IdMateria', $materiasPensumIds) // Que pertenezca a sus materias permitidas
            ->where('cursos_materias.Estado', 1)
            
            // FILTRO DE PRERREQUISITOS (IdMateriaPrevia):
            ->where(function ($query) use ($estudianteCarrera) {
                $query->whereNull('materias.IdMateriaPrevia') // Si no requiere materia previa, califica directo
                      ->orWhereExists(function ($subQuery) use ($estudianteCarrera) {
                          // Si requiere una materia previa, buscamos que esté aprobada con nota >= 51 en la tabla 'notas'
                          $subQuery->select(DB::raw(1))
                              ->from('notas')
                              ->join('inscripciones', 'notas.IdInscripcion', '=', 'inscripciones.IdInscripcion')
                              ->join('cursos_materias as cm_previa', 'inscripciones.IdCursoMateria', '=', 'cm_previa.IdCursoMateria')
                              ->where('inscripciones.IdEstudiante', $estudianteCarrera->IdEstudianteCarrera)
                              ->whereColumn('cm_previa.IdMateria', 'materias.IdMateriaPrevia') // Compara con el ID de la materia previa requerida
                              ->where('notas.Nota', '>=', 51.00) // Nota aprobatoria
                              ->where('notas.Estado', 1)
                              ->where('inscripciones.Estado', 1);
                      });
            })
            ->select(
                'cursos_materias.IdCursoMateria',
                'cursos.Nombre as Curso',
                'cursos.Aula',
                'materias.Nombre as Materia',
                'turnos.Nombre as Turno',
                'cursos_materias.FechaInicio',
                'cursos_materias.FechaFin'
            )
            ->get();
    }

    /**
     * RF05 - Obtiene detalles específicos de un registro de cursos_materias
     */
    public function findCursoMateriaConDetalles(int $idCursoMateria)
    {
        return DB::table('cursos_materias')
            ->where('IdCursoMateria', $idCursoMateria)
            ->first();
    }

    /**
     * RF05 - Regla de negocio: Revisa duplicado de materia o cruce de horarios en su registro de estudiante
     */
    public function verificarCruceODuplicado(int $idUsuario, int $idMateria, int $idTurno): bool
    {
        $estudianteCarrera = $this->getModalidadEstudiante($idUsuario);

        if (!$estudianteCarrera) {
            return false;
        }

        return DB::table('inscripciones')
            ->join('cursos_materias', 'inscripciones.IdCursoMateria', '=', 'cursos_materias.IdCursoMateria')
            ->where('inscripciones.IdEstudiante', $estudianteCarrera->IdEstudianteCarrera)
            ->where('inscripciones.Estado', 1)
            ->where(function ($query) use ($idMateria, $idTurno) {
                $query->where('cursos_materias.IdMateria', $idMateria) // Misma Materia
                      ->orWhere('cursos_materias.IdTurno', $idTurno);  // Mismo Horario/Turno
            })
            ->exists();
    }

    /**
     * RF05 - Registra de forma definitiva la inscripción apuntando a IdEstudiante
     */
    public function registrarInscripcionMateria(int $idUsuario, int $idCursoMateria): bool
    {
        $estudianteCarrera = $this->getModalidadEstudiante($idUsuario);

        if (!$estudianteCarrera) {
            return false;
        }

        return DB::table('inscripciones')->insert([
            'IdEstudiante'   => $estudianteCarrera->IdEstudianteCarrera, // Llave foránea real de tu script
            'IdCursoMateria' => $idCursoMateria,
            'Fecha'          => now(),
            'Estado'         => 1,
            'Aprobado'       => 0
        ]);
    }
}