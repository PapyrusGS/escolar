<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['Nombre' => 'Administrador', 'Descripcion' => 'Control total del sistema', 'Estado' => true],
            ['Nombre' => 'Docente', 'Descripcion' => 'Gestion de calificaciones y cursos', 'Estado' => true],
            ['Nombre' => 'Estudiante', 'Descripcion' => 'Inscripcion y visualizacion de cursos', 'Estado' => true],
        ]);
    }
}
