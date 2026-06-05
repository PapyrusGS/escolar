<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos')->insert([
            // Piso 1 - 101 al 110
            ['Piso' => 1, 'Aula' => 'Aula 101', 'Nombre' => 'Curso 101'],
            ['Piso' => 1, 'Aula' => 'Aula 102', 'Nombre' => 'Curso 102'],
            ['Piso' => 1, 'Aula' => 'Aula 103', 'Nombre' => 'Curso 103'],
            ['Piso' => 1, 'Aula' => 'Aula 104', 'Nombre' => 'Curso 104'],
            ['Piso' => 1, 'Aula' => 'Aula 105', 'Nombre' => 'Curso 105'],
            ['Piso' => 1, 'Aula' => 'Aula 106', 'Nombre' => 'Curso 106'],
            ['Piso' => 1, 'Aula' => 'Aula 107', 'Nombre' => 'Curso 107'],
            ['Piso' => 1, 'Aula' => 'Aula 108', 'Nombre' => 'Curso 108'],
            ['Piso' => 1, 'Aula' => 'Aula 109', 'Nombre' => 'Curso 109'],
            ['Piso' => 1, 'Aula' => 'Aula 110', 'Nombre' => 'Curso 110'],

            // Piso 2 - 201 al 210
            ['Piso' => 2, 'Aula' => 'Aula 201', 'Nombre' => 'Curso 201'],
            ['Piso' => 2, 'Aula' => 'Aula 202', 'Nombre' => 'Curso 202'],
            ['Piso' => 2, 'Aula' => 'Aula 203', 'Nombre' => 'Curso 203'],
            ['Piso' => 2, 'Aula' => 'Aula 204', 'Nombre' => 'Curso 204'],
            ['Piso' => 2, 'Aula' => 'Aula 205', 'Nombre' => 'Curso 205'],
            ['Piso' => 2, 'Aula' => 'Aula 206', 'Nombre' => 'Curso 206'],
            ['Piso' => 2, 'Aula' => 'Aula 207', 'Nombre' => 'Curso 207'],
            ['Piso' => 2, 'Aula' => 'Aula 208', 'Nombre' => 'Curso 208'],
            ['Piso' => 2, 'Aula' => 'Aula 209', 'Nombre' => 'Curso 209'],
            ['Piso' => 2, 'Aula' => 'Aula 210', 'Nombre' => 'Curso 210'],

            // Piso 3 - 301 al 310
            ['Piso' => 3, 'Aula' => 'Aula 301', 'Nombre' => 'Curso 301'],
            ['Piso' => 3, 'Aula' => 'Aula 302', 'Nombre' => 'Curso 302'],
            ['Piso' => 3, 'Aula' => 'Aula 303', 'Nombre' => 'Curso 303'],
            ['Piso' => 3, 'Aula' => 'Aula 304', 'Nombre' => 'Curso 304'],
            ['Piso' => 3, 'Aula' => 'Aula 305', 'Nombre' => 'Curso 305'],
            ['Piso' => 3, 'Aula' => 'Aula 306', 'Nombre' => 'Curso 306'],
            ['Piso' => 3, 'Aula' => 'Aula 307', 'Nombre' => 'Curso 307'],
            ['Piso' => 3, 'Aula' => 'Aula 308', 'Nombre' => 'Curso 308'],
            ['Piso' => 3, 'Aula' => 'Aula 309', 'Nombre' => 'Curso 309'],
            ['Piso' => 3, 'Aula' => 'Aula 310', 'Nombre' => 'Curso 310'],
        ]);
    }
}