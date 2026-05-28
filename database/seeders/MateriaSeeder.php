<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        // Materias sin prerequisito
        DB::table('materias')->insert([
            ['IdCarrera' => 1, 'IdMateriaPrevia' => null, 'Nombre' => 'Matematicas I', 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateriaPrevia' => null, 'Nombre' => 'Programacion I', 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateriaPrevia' => null, 'Nombre' => 'Algebra Lineal', 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateriaPrevia' => null, 'Nombre' => 'Fisica I', 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateriaPrevia' => null, 'Nombre' => 'Base de Datos I', 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateriaPrevia' => null, 'Nombre' => 'Contabilidad I', 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateriaPrevia' => null, 'Nombre' => 'Matematica Financiera', 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateriaPrevia' => null, 'Nombre' => 'Auditoria I', 'Estado' => true],
            ['IdCarrera' => 3, 'IdMateriaPrevia' => null, 'Nombre' => 'Diseno Basico', 'Estado' => true],
            ['IdCarrera' => 3, 'IdMateriaPrevia' => null, 'Nombre' => 'Teoria del Color', 'Estado' => true],
            ['IdCarrera' => 4, 'IdMateriaPrevia' => null, 'Nombre' => 'Administracion I', 'Estado' => true],
            ['IdCarrera' => 5, 'IdMateriaPrevia' => null, 'Nombre' => 'Derecho Civil', 'Estado' => true],
            ['IdCarrera' => 5, 'IdMateriaPrevia' => null, 'Nombre' => 'Derecho Penal', 'Estado' => true],
        ]);

        // Materias con prerequisito
        DB::table('materias')->insert([
            ['IdCarrera' => 1, 'IdMateriaPrevia' => 1, 'Nombre' => 'Matematicas II', 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateriaPrevia' => 2, 'Nombre' => 'Programacion II', 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateriaPrevia' => 3, 'Nombre' => 'Calculo I', 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateriaPrevia' => 4, 'Nombre' => 'Fisica II', 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateriaPrevia' => 5, 'Nombre' => 'Base de Datos II', 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateriaPrevia' => 6, 'Nombre' => 'Contabilidad II', 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateriaPrevia' => 8, 'Nombre' => 'Auditoria II', 'Estado' => true],
            ['IdCarrera' => 4, 'IdMateriaPrevia' => 11, 'Nombre' => 'Administracion II', 'Estado' => true],
        ]);
    }
}
