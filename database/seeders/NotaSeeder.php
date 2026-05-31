<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notas')->insert([
            ['IdInscripcion' => 1,  'Nota' => 85.50, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 2,  'Nota' => 92.00, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 3,  'Nota' => 78.00, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 4,  'Nota' => 65.50, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 5,  'Nota' => 90.00, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 6,  'Nota' => 88.50, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 7,  'Nota' => 72.00, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 8,  'Nota' => 95.00, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 9,  'Nota' => 60.00, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdInscripcion' => 10, 'Nota' => 55.50, 'FechaRegistro' => now(), 'Estado' => true],
        ]);
    }
}
