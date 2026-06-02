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

        $this->command?->info('Aprobado recalculado según la vigencia de cada curso.');
    }
}
