<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoMateriaSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = Carbon::now();

        DB::table('cursos_materias')->insert([
            ['IdCurso' => 1, 'IdMateria' => 1,  'IdDocente' => 2,  'IdTurno' => 1, 'FechaInicio' => $hoy->copy()->subDays(7)->toDateString(),  'FechaFin' => $hoy->copy()->addDays(21)->toDateString(), 'MaxInscritos' => 40, 'Inscritos' => 2, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 1, 'IdMateria' => 2,  'IdDocente' => 2,  'IdTurno' => 3, 'FechaInicio' => $hoy->copy()->subDays(7)->toDateString(),  'FechaFin' => $hoy->copy()->addDays(21)->toDateString(), 'MaxInscritos' => 35, 'Inscritos' => 2, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 2, 'IdMateria' => 14, 'IdDocente' => 5,  'IdTurno' => 1, 'FechaInicio' => $hoy->copy()->subDays(7)->toDateString(),  'FechaFin' => $hoy->copy()->addDays(21)->toDateString(), 'MaxInscritos' => 40, 'Inscritos' => 3, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 2, 'IdMateria' => 15, 'IdDocente' => 5,  'IdTurno' => 2, 'FechaInicio' => $hoy->copy()->subDays(7)->toDateString(),  'FechaFin' => $hoy->copy()->addDays(21)->toDateString(), 'MaxInscritos' => 35, 'Inscritos' => 2, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 2, 'IdMateria' => 6,  'IdDocente' => 15, 'IdTurno' => 3, 'FechaInicio' => $hoy->copy()->addDays(30)->toDateString(), 'FechaFin' => $hoy->copy()->addDays(60)->toDateString(), 'MaxInscritos' => 30, 'Inscritos' => 3, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 3, 'IdMateria' => 3,  'IdDocente' => 15, 'IdTurno' => 1, 'FechaInicio' => $hoy->copy()->addDays(30)->toDateString(), 'FechaFin' => $hoy->copy()->addDays(60)->toDateString(), 'MaxInscritos' => 25, 'Inscritos' => 2, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 3, 'IdMateria' => 4,  'IdDocente' => 16, 'IdTurno' => 2, 'FechaInicio' => $hoy->copy()->subDays(45)->toDateString(), 'FechaFin' => $hoy->copy()->subDays(15)->toDateString(), 'MaxInscritos' => 40, 'Inscritos' => 0, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 3, 'IdMateria' => 5,  'IdDocente' => 16, 'IdTurno' => 3, 'FechaInicio' => $hoy->copy()->subDays(45)->toDateString(), 'FechaFin' => $hoy->copy()->subDays(15)->toDateString(), 'MaxInscritos' => 30, 'Inscritos' => 1, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 4, 'IdMateria' => 7,  'IdDocente' => 5,  'IdTurno' => 1, 'FechaInicio' => $hoy->copy()->subDays(7)->toDateString(),  'FechaFin' => $hoy->copy()->addDays(21)->toDateString(), 'MaxInscritos' => 35, 'Inscritos' => 2, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 4, 'IdMateria' => 8,  'IdDocente' => 15, 'IdTurno' => 2, 'FechaInicio' => $hoy->copy()->addDays(30)->toDateString(), 'FechaFin' => $hoy->copy()->addDays(60)->toDateString(), 'MaxInscritos' => 40, 'Inscritos' => 2, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdCurso' => 4, 'IdMateria' => 11, 'IdDocente' => 16, 'IdTurno' => 3, 'FechaInicio' => $hoy->copy()->subDays(45)->toDateString(), 'FechaFin' => $hoy->copy()->subDays(15)->toDateString(), 'MaxInscritos' => 20, 'Inscritos' => 0, 'FechaRegistro' => now(), 'Estado' => true],
        ]);
    }
}

