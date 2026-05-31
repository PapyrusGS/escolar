<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraMateriaPensumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definimos las variables para que puedas cambiarlas fácilmente en el futuro
        $idCarrera = 1; // Id de la Carrera guardada
        $idPensum = 1;  // Id del Pensum guardado

        // Ejecutamos tu consulta SQL adaptada a los métodos de Laravel
        DB::statement("
            INSERT INTO carreraMateriaPensum (IdCarrera, IdMateria, IdPensum, Semestre, FechaRegistro, Estado)
            SELECT 
                :idCarrera, 
                IdMateria, 
                :idPensum, 
                CAST(SUBSTRING(CodigoMateria, 5, 1) AS UNSIGNED),
                NOW(),
                1
            FROM materias 
            WHERE IdMateria BETWEEN 1 AND 54
        ", [
            'idCarrera' => $idCarrera,
            'idPensum' => $idPensum
        ]);
    }
}