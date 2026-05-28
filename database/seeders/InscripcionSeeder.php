<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inscripciones')->insert([
            ['IdEstudiante' => 3, 'IdCursoMateria' => 1, 'Estado' => true],
            ['IdEstudiante' => 3, 'IdCursoMateria' => 2, 'Estado' => true],
            ['IdEstudiante' => 6, 'IdCursoMateria' => 3, 'Estado' => true],
            ['IdEstudiante' => 6, 'IdCursoMateria' => 4, 'Estado' => true],
            ['IdEstudiante' => 7, 'IdCursoMateria' => 1, 'Estado' => true],
            ['IdEstudiante' => 7, 'IdCursoMateria' => 2, 'Estado' => true],
            ['IdEstudiante' => 8, 'IdCursoMateria' => 3, 'Estado' => true],
            ['IdEstudiante' => 8, 'IdCursoMateria' => 5, 'Estado' => true],
            ['IdEstudiante' => 9, 'IdCursoMateria' => 5, 'Estado' => true],
            ['IdEstudiante' => 9, 'IdCursoMateria' => 6, 'Estado' => true],
            ['IdEstudiante' => 10, 'IdCursoMateria' => 9, 'Estado' => true],
            ['IdEstudiante' => 10, 'IdCursoMateria' => 10, 'Estado' => true],
            ['IdEstudiante' => 11, 'IdCursoMateria' => 3, 'Estado' => true],
            ['IdEstudiante' => 11, 'IdCursoMateria' => 4, 'Estado' => true],
            ['IdEstudiante' => 12, 'IdCursoMateria' => 5, 'Estado' => true],
            ['IdEstudiante' => 12, 'IdCursoMateria' => 9, 'Estado' => true],
            ['IdEstudiante' => 13, 'IdCursoMateria' => 6, 'Estado' => true],
            ['IdEstudiante' => 13, 'IdCursoMateria' => 8, 'Estado' => true],
            ['IdEstudiante' => 14, 'IdCursoMateria' => 10, 'Estado' => true],
        ]);
    }
}
