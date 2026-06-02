<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inscripciones')->insert([
            ['IdEstudiante' => 1, 'IdCursoMateria' => 1, 'Fecha' => now(), 'Estado' => true, 'Aprobado' => 0],
            ['IdEstudiante' => 1, 'IdCursoMateria' => 2, 'Fecha' => now(), 'Estado' => true, 'Aprobado' => 0],

            ['IdEstudiante' => 2, 'IdCursoMateria' => 9, 'Fecha' => now()->subDays(30), 'Estado' => true, 'Aprobado' => 0],
            ['IdEstudiante' => 2, 'IdCursoMateria' => 10, 'Fecha' => now()->subDays(30), 'Estado' => true, 'Aprobado' => 0],

            ['IdEstudiante' => 3, 'IdCursoMateria' => 1, 'Fecha' => now(), 'Estado' => true, 'Aprobado' => 0],
            ['IdEstudiante' => 3, 'IdCursoMateria' => 2, 'Fecha' => now(), 'Estado' => true, 'Aprobado' => 0],

            ['IdEstudiante' => 4, 'IdCursoMateria' => 9, 'Fecha' => now()->subDays(30), 'Estado' => true, 'Aprobado' => 1],
            ['IdEstudiante' => 4, 'IdCursoMateria' => 11, 'Fecha' => now()->subDays(30), 'Estado' => true, 'Aprobado' => 1],

            ['IdEstudiante' => 5, 'IdCursoMateria' => 9, 'Fecha' => now()->subDays(30), 'Estado' => true, 'Aprobado' => 1],
            ['IdEstudiante' => 5, 'IdCursoMateria' => 10, 'Fecha' => now()->subDays(30), 'Estado' => true, 'Aprobado' => 0],
        ]);
    }
}

