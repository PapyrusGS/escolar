<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalidadSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('modalidad')->insert([
            [
                'Nombre' => 'Modular',
                'DuracionSemanasxMaterias' => 4,
                'MaxMaterias' => 2,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Semestral',
                'DuracionSemanasxMaterias' => 24,
                'MaxMaterias' => 6,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Anual',
                'DuracionSemanasxMaterias' => 48,
                'MaxMaterias' => 10,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
        ]);
    }
}
