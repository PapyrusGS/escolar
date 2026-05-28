<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalidadSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('modalidad')->insert([
            ['Nombre' => 'Modular', 'DuracionSemanas' => 4, 'MaxMaterias' => 2, 'Estado' => true],
            ['Nombre' => 'Semestral', 'DuracionSemanas' => 24, 'MaxMaterias' => 6, 'Estado' => true],
            ['Nombre' => 'Anual', 'DuracionSemanas' => 48, 'MaxMaterias' => 10, 'Estado' => true],
        ]);
    }
}
