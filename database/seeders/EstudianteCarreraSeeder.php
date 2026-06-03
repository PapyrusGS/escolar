<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstudianteCarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('EstudianteCarrera')->insert([
            ['IdUsuario' => 3, 'IdCarrera' => 1, 'IdModalidad' => 1, 'FechaRegistro' => now(), 'Estado' => true], // Maria Flores -> Ingenieria de Sistemas
            ['IdUsuario' => 6, 'IdCarrera' => 1, 'IdModalidad' => 1, 'FechaRegistro' => now(), 'Estado' => true], // Kike Torrez -> Ingenieria de Sistemas
            ['IdUsuario' => 7, 'IdCarrera' => 1, 'IdModalidad' => 1, 'FechaRegistro' => now(), 'Estado' => true], // Ana Condori -> Ingenieria de Sistemas
            ['IdUsuario' => 8, 'IdCarrera' => 1, 'IdModalidad' => 1, 'FechaRegistro' => now(), 'Estado' => true], // Pedro Vargas -> Ingenieria de Sistemas
            ['IdUsuario' => 9, 'IdCarrera' => 1, 'IdModalidad' => 1, 'FechaRegistro' => now(), 'Estado' => true], // Lucia Rios -> Ingenieria de Sistemas
            ['IdUsuario' => 10, 'IdCarrera' => 1, 'IdModalidad' => 2, 'FechaRegistro' => now(), 'Estado' => true], // Diego Mendoza -> Ingenieria de Sistemas (SEMESTRAL)
        ]);
    }
}
