<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materias')->insert([
            // --- PRIMER SEMESTRE (IDs: 1 - 6) ---
            ['IdMateria' => 1, 'CodigoMateria' => 'SIS-101', 'Nombre' => 'Introducción a la Ingeniería de Sistemas', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 2, 'CodigoMateria' => 'SIS-102', 'Nombre' => 'Matemática I', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 3, 'CodigoMateria' => 'SIS-103', 'Nombre' => 'Introducción a la Programación', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 4, 'CodigoMateria' => 'SIS-104', 'Nombre' => 'Fundamentos de Computación', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 5, 'CodigoMateria' => 'SIS-105', 'Nombre' => 'Comunicación Oral y Escrita', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 6, 'CodigoMateria' => 'SIS-106', 'Nombre' => 'Metodología de Estudio e Investigación', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- SEGUNDO SEMESTRE (IDs: 7 - 12) ---
            ['IdMateria' => 7, 'CodigoMateria' => 'SIS-201', 'Nombre' => 'Matemática II', 'IdMateriaPrevia' => 2, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 8, 'CodigoMateria' => 'SIS-202', 'Nombre' => 'Física I', 'IdMateriaPrevia' => 2, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 9, 'CodigoMateria' => 'SIS-203', 'Nombre' => 'Programación I', 'IdMateriaPrevia' => 3, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 10, 'CodigoMateria' => 'SIS-204', 'Nombre' => 'Arquitectura de Computadoras', 'IdMateriaPrevia' => 4, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 11, 'CodigoMateria' => 'SIS-205', 'Nombre' => 'Algoritmos y Estructuras de Datos', 'IdMateriaPrevia' => 9, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 12, 'CodigoMateria' => 'SIS-206', 'Nombre' => 'Estadística Descriptiva', 'IdMateriaPrevia' => 2, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- TERCER SEMESTRE (IDs: 13 - 18) ---
            ['IdMateria' => 13, 'CodigoMateria' => 'SIS-301', 'Nombre' => 'Matemática III', 'IdMateriaPrevia' => 7, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 14, 'CodigoMateria' => 'SIS-302', 'Nombre' => 'Programación II', 'IdMateriaPrevia' => 9, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 15, 'CodigoMateria' => 'SIS-303', 'Nombre' => 'Programación Orientada a Objetos', 'IdMateriaPrevia' => 14, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 16, 'CodigoMateria' => 'SIS-304', 'Nombre' => 'Base de Datos I', 'IdMateriaPrevia' => 9, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 17, 'CodigoMateria' => 'SIS-305', 'Nombre' => 'Sistemas Operativos I', 'IdMateriaPrevia' => 10, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 18, 'CodigoMateria' => 'SIS-306', 'Nombre' => 'Física II', 'IdMateriaPrevia' => 8, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- CUARTO SEMESTRE (IDs: 19 - 24) ---
            ['IdMateria' => 19, 'CodigoMateria' => 'SIS-401', 'Nombre' => 'Base de Datos II', 'IdMateriaPrevia' => 16, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 20, 'CodigoMateria' => 'SIS-402', 'Nombre' => 'Redes I', 'IdMateriaPrevia' => 10, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 21, 'CodigoMateria' => 'SIS-403', 'Nombre' => 'Sistemas Operativos II', 'IdMateriaPrevia' => 17, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 22, 'CodigoMateria' => 'SIS-404', 'Nombre' => 'Ingeniería de Software I', 'IdMateriaPrevia' => 15, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 23, 'CodigoMateria' => 'SIS-405', 'Nombre' => 'Desarrollo Web I', 'IdMateriaPrevia' => 14, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 24, 'CodigoMateria' => 'SIS-406', 'Nombre' => 'Probabilidad y Estadística', 'IdMateriaPrevia' => 12, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- QUINTO SEMESTRE (IDs: 25 - 30) ---
            ['IdMateria' => 25, 'CodigoMateria' => 'SIS-501', 'Nombre' => 'Redes II', 'IdMateriaPrevia' => 20, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 26, 'CodigoMateria' => 'SIS-502', 'Nombre' => 'Ingeniería de Software II', 'IdMateriaPrevia' => 22, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 27, 'CodigoMateria' => 'SIS-503', 'Nombre' => 'Desarrollo Web II', 'IdMateriaPrevia' => 23, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 28, 'CodigoMateria' => 'SIS-504', 'Nombre' => 'Desarrollo Móvil I', 'IdMateriaPrevia' => 15, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 29, 'CodigoMateria' => 'SIS-505', 'Nombre' => 'Base de Datos Avanzada', 'IdMateriaPrevia' => 19, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 30, 'CodigoMateria' => 'SIS-506', 'Nombre' => 'Seguridad Informática I', 'IdMateriaPrevia' => 20, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- SEXTO SEMESTRE (IDs: 31 - 36) ---
            ['IdMateria' => 31, 'CodigoMateria' => 'SIS-601', 'Nombre' => 'Arquitectura de Software', 'IdMateriaPrevia' => 26, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 32, 'CodigoMateria' => 'SIS-602', 'Nombre' => 'Desarrollo Móvil II', 'IdMateriaPrevia' => 28, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 33, 'CodigoMateria' => 'SIS-603', 'Nombre' => 'Seguridad Informática II', 'IdMateriaPrevia' => 30, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 34, 'CodigoMateria' => 'SIS-604', 'Nombre' => 'Administración de Servidores', 'IdMateriaPrevia' => 21, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 35, 'CodigoMateria' => 'SIS-605', 'Nombre' => 'Inteligencia Artificial I', 'IdMateriaPrevia' => 13, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 36, 'CodigoMateria' => 'SIS-606', 'Nombre' => 'Gestión de Proyectos de Software', 'IdMateriaPrevia' => 26, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- SÉPTIMO SEMESTRE (IDs: 37 - 42) ---
            ['IdMateria' => 37, 'CodigoMateria' => 'SIS-701', 'Nombre' => 'Inteligencia Artificial II', 'IdMateriaPrevia' => 35, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 38, 'CodigoMateria' => 'SIS-702', 'Nombre' => 'Computación en la Nube', 'IdMateriaPrevia' => 34, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 39, 'CodigoMateria' => 'SIS-703', 'Nombre' => 'Auditoría de Sistemas', 'IdMateriaPrevia' => 33, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 40, 'CodigoMateria' => 'SIS-704', 'Nombre' => 'Minería de Datos', 'IdMateriaPrevia' => 29, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 41, 'CodigoMateria' => 'SIS-705', 'Nombre' => 'Redes Avanzadas', 'IdMateriaPrevia' => 25, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 42, 'CodigoMateria' => 'SIS-706', 'Nombre' => 'Sistemas Distribuidos', 'IdMateriaPrevia' => 31, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- OCTAVO SEMESTRE (IDs: 43 - 48) ---
            ['IdMateria' => 43, 'CodigoMateria' => 'SIS-801', 'Nombre' => 'Desarrollo de APIs y Microservicios', 'IdMateriaPrevia' => 31, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 44, 'CodigoMateria' => 'SIS-802', 'Nombre' => 'Ciberseguridad', 'IdMateriaPrevia' => 33, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 45, 'CodigoMateria' => 'SIS-803', 'Nombre' => 'Business Intelligence', 'IdMateriaPrevia' => 40, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 46, 'CodigoMateria' => 'SIS-804', 'Nombre' => 'Internet de las Cosas (IoT)', 'IdMateriaPrevia' => 41, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 47, 'CodigoMateria' => 'SIS-805', 'Nombre' => 'Gestión de Calidad de Software', 'IdMateriaPrevia' => 36, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 48, 'CodigoMateria' => 'SIS-806', 'Nombre' => 'Taller de Investigación', 'IdMateriaPrevia' => 6, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- NOVENO SEMESTRE (IDs: 49 - 54) ---
            ['IdMateria' => 49, 'CodigoMateria' => 'SIS-901', 'Nombre' => 'Proyecto de Grado', 'IdMateriaPrevia' => 48, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 50, 'CodigoMateria' => 'SIS-902', 'Nombre' => 'Práctica Profesional', 'IdMateriaPrevia' => 48, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 51, 'CodigoMateria' => 'SIS-903', 'Nombre' => 'Ética Profesional', 'IdMateriaPrevia' => 1, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 52, 'CodigoMateria' => 'SIS-904', 'Nombre' => 'Gestión de Innovación Tecnológica', 'IdMateriaPrevia' => 36, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 53, 'CodigoMateria' => 'SIS-905', 'Nombre' => 'Seminario de Actualización Tecnológica', 'IdMateriaPrevia' => 48, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 54, 'CodigoMateria' => 'SIS-906', 'Nombre' => 'Formulación y Evaluación de Proyectos', 'IdMateriaPrevia' => 36, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
        ]);
    }
}
