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
            ['Piso' => 1, 'Aula' => 'Aula 101'],
            ['Piso' => 1, 'Aula' => 'Aula 102'],
            ['Piso' => 1, 'Aula' => 'Aula 103'],
            ['Piso' => 1, 'Aula' => 'Aula 104'],
            ['Piso' => 1, 'Aula' => 'Aula 105'],
            ['Piso' => 1, 'Aula' => 'Aula 106'],
            ['Piso' => 1, 'Aula' => 'Aula 107'],
            ['Piso' => 1, 'Aula' => 'Aula 108'],
            ['Piso' => 1, 'Aula' => 'Aula 109'],
            ['Piso' => 1, 'Aula' => 'Aula 110'],

            // Piso 2 - 201 al 210
            ['Piso' => 2, 'Aula' => 'Aula 201'],
            ['Piso' => 2, 'Aula' => 'Aula 202'],
            ['Piso' => 2, 'Aula' => 'Aula 203'],
            ['Piso' => 2, 'Aula' => 'Aula 204'],
            ['Piso' => 2, 'Aula' => 'Aula 205'],
            ['Piso' => 2, 'Aula' => 'Aula 206'],
            ['Piso' => 2, 'Aula' => 'Aula 207'],
            ['Piso' => 2, 'Aula' => 'Aula 208'],
            ['Piso' => 2, 'Aula' => 'Aula 209'],
            ['Piso' => 2, 'Aula' => 'Aula 210'],

            // Piso 3 - 301 al 310
            ['Piso' => 3, 'Aula' => 'Aula 301'],
            ['Piso' => 3, 'Aula' => 'Aula 302'],
            ['Piso' => 3, 'Aula' => 'Aula 303'],
            ['Piso' => 3, 'Aula' => 'Aula 304'],
            ['Piso' => 3, 'Aula' => 'Aula 305'],
            ['Piso' => 3, 'Aula' => 'Aula 306'],
            ['Piso' => 3, 'Aula' => 'Aula 307'],
            ['Piso' => 3, 'Aula' => 'Aula 308'],
            ['Piso' => 3, 'Aula' => 'Aula 309'],
            ['Piso' => 3, 'Aula' => 'Aula 310'],
        ]);
    }
}