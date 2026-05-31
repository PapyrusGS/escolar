<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos')->insert([
            ['Piso' => 1, 'Aula' => 'Aula 101'],
            ['Piso' => 1, 'Aula' => 'Aula 102'],
            ['Piso' => 2, 'Aula' => 'Aula 201'],
            ['Piso' => 2, 'Aula' => 'Aula 202'],
        ]);
    }
}
