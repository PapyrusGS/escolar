<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inscripciones')->insert([
            ['IdEstudiante' => 1, 'IdCursoMateria' => 1, 'Fecha' => now(), 'Estado' => true],
            ['IdEstudiante' => 1, 'IdCursoMateria' => 2, 'Fecha' => now(), 'Estado' => true],
            
            ['IdEstudiante' => 2, 'IdCursoMateria' => 3, 'Fecha' => now(), 'Estado' => true],
            ['IdEstudiante' => 2, 'IdCursoMateria' => 4, 'Fecha' => now(), 'Estado' => true],
            
            ['IdEstudiante' => 3, 'IdCursoMateria' => 1, 'Fecha' => now(), 'Estado' => true],
            ['IdEstudiante' => 3, 'IdCursoMateria' => 2, 'Fecha' => now(), 'Estado' => true],
            
            ['IdEstudiante' => 4, 'IdCursoMateria' => 3, 'Fecha' => now(), 'Estado' => true],
            ['IdEstudiante' => 4, 'IdCursoMateria' => 5, 'Fecha' => now(), 'Estado' => true],
            
            ['IdEstudiante' => 5, 'IdCursoMateria' => 5, 'Fecha' => now(), 'Estado' => true],
            ['IdEstudiante' => 5, 'IdCursoMateria' => 6, 'Fecha' => now(), 'Estado' => true],
        ]);
    }
}
