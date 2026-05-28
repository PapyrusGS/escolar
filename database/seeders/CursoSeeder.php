<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos')->insert([
            ['Nombre' => 'Curso 1', 'Descripcion' => 'Primer curso académico', 'Estado' => true],
            ['Nombre' => 'Curso 2', 'Descripcion' => 'Segundo curso académico', 'Estado' => true],
            ['Nombre' => 'Curso 3', 'Descripcion' => 'Tercer curso académico', 'Estado' => true],
            ['Nombre' => 'Curso 4', 'Descripcion' => 'Cuarto curso académico', 'Estado' => true],
        ]);
    }
}
