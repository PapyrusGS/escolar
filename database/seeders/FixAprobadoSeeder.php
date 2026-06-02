<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixAprobadoSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = Carbon::now()->toDateString();

        DB::table('inscripciones as i')
            ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
            ->where('i.Estado', 1)
            ->where('cm.Estado', 1)
            ->where('cm.FechaFin', '>=', $hoy)
            ->update(['i.Aprobado' => 0]);

        DB::table('inscripciones as i')
            ->join('cursos_materias as cm', 'i.IdCursoMateria', '=', 'cm.IdCursoMateria')
            ->where('i.Estado', 1)
            ->where('cm.Estado', 1)
            ->where('cm.FechaFin', '<', $hoy)
            ->update(['i.Aprobado' => 1]);

        DB::table('materias_aprobadas')->truncate();

        DB::statement(<<<SQL
            INSERT INTO materias_aprobadas (IdUsuario, IdMateria, IdCarrera, NotaFinal, FechaAprobacion, Estado)
            SELECT ec.IdUsuario, cm.IdMateria, ec.IdCarrera, MAX(n.Nota), CURRENT_DATE, 1
            FROM notas n
            JOIN inscripciones i ON n.IdInscripcion = i.IdInscripcion
            JOIN cursos_materias cm ON i.IdCursoMateria = cm.IdCursoMateria
            JOIN EstudianteCarrera ec ON i.IdEstudiante = ec.IdEstudianteCarrera
            WHERE n.Estado = 1
              AND n.Nota >= 51
              AND i.Estado = 1
              AND cm.Estado = 1
            GROUP BY ec.IdUsuario, cm.IdMateria, ec.IdCarrera
        SQL);

        $this->command?->info('Aprobado recalculado y materias_aprobadas regeneradas desde notas >= 51.');
    }
}
