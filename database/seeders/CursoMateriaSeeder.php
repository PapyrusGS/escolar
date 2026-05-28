<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoMateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos_materias')->insert([
            ['IdCurso' => 1, 'IdMateria' => 1, 'IdDocente' => 2, 'IdTurno' => 1, 'FechaInicio' => '2025-06-01', 'FechaFin' => '2025-06-28', 'MaxInscritos' => 40, 'Estado' => true],
            ['IdCurso' => 1, 'IdMateria' => 2, 'IdDocente' => 2, 'IdTurno' => 3, 'FechaInicio' => '2025-06-01', 'FechaFin' => '2025-06-28', 'MaxInscritos' => 35, 'Estado' => true],
            ['IdCurso' => 2, 'IdMateria' => 14, 'IdDocente' => 5, 'IdTurno' => 1, 'FechaInicio' => '2025-07-01', 'FechaFin' => '2025-07-28', 'MaxInscritos' => 40, 'Estado' => true],
            ['IdCurso' => 2, 'IdMateria' => 15, 'IdDocente' => 5, 'IdTurno' => 2, 'FechaInicio' => '2025-07-01', 'FechaFin' => '2025-07-28', 'MaxInscritos' => 35, 'Estado' => true],
            ['IdCurso' => 2, 'IdMateria' => 6, 'IdDocente' => 15, 'IdTurno' => 3, 'FechaInicio' => '2025-07-01', 'FechaFin' => '2025-07-28', 'MaxInscritos' => 30, 'Estado' => true],
            ['IdCurso' => 3, 'IdMateria' => 3, 'IdDocente' => 15, 'IdTurno' => 1, 'FechaInicio' => '2025-08-01', 'FechaFin' => '2025-08-28', 'MaxInscritos' => 25, 'Estado' => true],
            ['IdCurso' => 3, 'IdMateria' => 4, 'IdDocente' => 16, 'IdTurno' => 2, 'FechaInicio' => '2025-08-01', 'FechaFin' => '2025-08-28', 'MaxInscritos' => 40, 'Estado' => true],
            ['IdCurso' => 3, 'IdMateria' => 5, 'IdDocente' => 16, 'IdTurno' => 3, 'FechaInicio' => '2025-08-01', 'FechaFin' => '2025-08-28', 'MaxInscritos' => 30, 'Estado' => true],
            ['IdCurso' => 4, 'IdMateria' => 7, 'IdDocente' => 5, 'IdTurno' => 1, 'FechaInicio' => '2025-09-01', 'FechaFin' => '2025-09-28', 'MaxInscritos' => 35, 'Estado' => true],
            ['IdCurso' => 4, 'IdMateria' => 8, 'IdDocente' => 15, 'IdTurno' => 2, 'FechaInicio' => '2025-09-01', 'FechaFin' => '2025-09-28', 'MaxInscritos' => 40, 'Estado' => true],
            ['IdCurso' => 4, 'IdMateria' => 11, 'IdDocente' => 16, 'IdTurno' => 3, 'FechaInicio' => '2025-09-01', 'FechaFin' => '2025-09-28', 'MaxInscritos' => 20, 'Estado' => true],
        ]);
    }
}
