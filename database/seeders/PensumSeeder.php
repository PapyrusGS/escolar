<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PensumSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pensum')->insert([
            ['IdModalidad' => 1, 'Nombre' => 'SistemasModular', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 2, 'Nombre' => 'SistemasSemestral', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 3, 'Nombre' => 'SistemasAnual', 'NumMaterias' => 52, 'NumSemestres' => 10, 'FechaRegistro' => now(), 'Estado' => true],
            
            ['IdModalidad' => 1, 'Nombre' => 'ContaduriaModular', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 2, 'Nombre' => 'ContaduriaSemestral', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 3, 'Nombre' => 'ContaduriaAnual', 'NumMaterias' => 52, 'NumSemestres' => 10, 'FechaRegistro' => now(), 'Estado' => true],
            
            ['IdModalidad' => 1, 'Nombre' => 'DisenoModular', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 2, 'Nombre' => 'DisenoSemestral', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 3, 'Nombre' => 'DisenoAnual', 'NumMaterias' => 52, 'NumSemestres' => 10, 'FechaRegistro' => now(), 'Estado' => true],
            
            ['IdModalidad' => 1, 'Nombre' => 'AdministracionModular', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 2, 'Nombre' => 'AdministracionSemestral', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 3, 'Nombre' => 'AdministracionAnual', 'NumMaterias' => 52, 'NumSemestres' => 10, 'FechaRegistro' => now(), 'Estado' => true],
            
            ['IdModalidad' => 1, 'Nombre' => 'DerechoModular', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 2, 'Nombre' => 'DerechoSemestral', 'NumMaterias' => 54, 'NumSemestres' => 9, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdModalidad' => 3, 'Nombre' => 'DerechoAnual', 'NumMaterias' => 52, 'NumSemestres' => 10, 'FechaRegistro' => now(), 'Estado' => true],
        ]);
    }
}
