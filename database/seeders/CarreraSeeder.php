<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carreras')->insert([
            ['IdModalidad' => 1, 'Nombre' => 'Ingenieria de Sistemas', 'Descripcion' => 'Carrera de tecnologia e informatica', 'Estado' => true],
            ['IdModalidad' => 2, 'Nombre' => 'Contaduria Publica', 'Descripcion' => 'Carrera de ciencias economicas', 'Estado' => true],
            ['IdModalidad' => 1, 'Nombre' => 'Diseno Grafico', 'Descripcion' => 'Carrera de artes visuales y comunicacion', 'Estado' => true],
            ['IdModalidad' => 2, 'Nombre' => 'Administracion', 'Descripcion' => 'Carrera de gestion empresarial', 'Estado' => true],
            ['IdModalidad' => 3, 'Nombre' => 'Derecho', 'Descripcion' => 'Carrera de ciencias juridicas', 'Estado' => true],
        ]);
    }
}
