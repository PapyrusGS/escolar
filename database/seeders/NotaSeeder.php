<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notas')->insert([
            ['IdEstudiante' => 3, 'IdCursoMateria' => 1, 'Nota' => 85.50, 'Estado' => true],
            ['IdEstudiante' => 3, 'IdCursoMateria' => 2, 'Nota' => 92.00, 'Estado' => true],
            ['IdEstudiante' => 6, 'IdCursoMateria' => 3, 'Nota' => 78.00, 'Estado' => true],
            ['IdEstudiante' => 6, 'IdCursoMateria' => 4, 'Nota' => 65.50, 'Estado' => true],
            ['IdEstudiante' => 7, 'IdCursoMateria' => 1, 'Nota' => 90.00, 'Estado' => true],
            ['IdEstudiante' => 7, 'IdCursoMateria' => 2, 'Nota' => 88.50, 'Estado' => true],
            ['IdEstudiante' => 8, 'IdCursoMateria' => 3, 'Nota' => 72.00, 'Estado' => true],
            ['IdEstudiante' => 8, 'IdCursoMateria' => 5, 'Nota' => 95.00, 'Estado' => true],
            ['IdEstudiante' => 9, 'IdCursoMateria' => 5, 'Nota' => 60.00, 'Estado' => true],
            ['IdEstudiante' => 9, 'IdCursoMateria' => 6, 'Nota' => 55.50, 'Estado' => true],
            ['IdEstudiante' => 10, 'IdCursoMateria' => 9, 'Nota' => 83.00, 'Estado' => true],
            ['IdEstudiante' => 10, 'IdCursoMateria' => 10, 'Nota' => 77.50, 'Estado' => true],
            ['IdEstudiante' => 11, 'IdCursoMateria' => 3, 'Nota' => 91.00, 'Estado' => true],
            ['IdEstudiante' => 11, 'IdCursoMateria' => 4, 'Nota' => 68.00, 'Estado' => true],
            ['IdEstudiante' => 12, 'IdCursoMateria' => 5, 'Nota' => 74.50, 'Estado' => true],
            ['IdEstudiante' => 12, 'IdCursoMateria' => 9, 'Nota' => 82.00, 'Estado' => true],
            ['IdEstudiante' => 13, 'IdCursoMateria' => 6, 'Nota' => 88.00, 'Estado' => true],
            ['IdEstudiante' => 13, 'IdCursoMateria' => 8, 'Nota' => 79.50, 'Estado' => true],
            ['IdEstudiante' => 14, 'IdCursoMateria' => 10, 'Nota' => 66.00, 'Estado' => true],
        ]);
    }
}
