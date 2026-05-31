<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carreras')->insert([
            ['Nombre' => 'Ingenieria de Sistemas', 'Descripcion' => 'Carrera de tecnologia e informatica', 'FechaRegistro' => now(), 'Estado' => true],
            ['Nombre' => 'Contaduria Publica', 'Descripcion' => 'Carrera de ciencias economicas', 'FechaRegistro' => now(), 'Estado' => true],
            ['Nombre' => 'Diseno Grafico', 'Descripcion' => 'Carrera de artes visuales y comunicacion', 'FechaRegistro' => now(), 'Estado' => true],
            ['Nombre' => 'Administracion', 'Descripcion' => 'Carrera de gestion empresarial', 'FechaRegistro' => now(), 'Estado' => true],
            ['Nombre' => 'Derecho', 'Descripcion' => 'Carrera de ciencias juridicas', 'FechaRegistro' => now(), 'Estado' => true],
        ]);
    }
}
