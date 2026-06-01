<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materias')->insert([
            // =========================================================================
            // INGENIERÍA DE SISTEMAS (IDs: 1 - 54) -> 9 Semestres x 6 Materias
            // =========================================================================
            
            // --- PRIMER SEMESTRE ---
            ['IdMateria' => 1, 'CodigoMateria' => 'SIS-101', 'Nombre' => 'Introducción a la Ingeniería de Sistemas', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 2, 'CodigoMateria' => 'SIS-102', 'Nombre' => 'Matemática I', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 3, 'CodigoMateria' => 'SIS-103', 'Nombre' => 'Introducción a la Programación', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 4, 'CodigoMateria' => 'SIS-104', 'Nombre' => 'Fundamentos de Computación', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 5, 'CodigoMateria' => 'SIS-105', 'Nombre' => 'Comunicación Oral y Escrita', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 6, 'CodigoMateria' => 'SIS-106', 'Nombre' => 'Metodología de Estudio e Investigación', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- SEGUNDO SEMESTRE ---
            ['IdMateria' => 7, 'CodigoMateria' => 'SIS-201', 'Nombre' => 'Matemática II', 'IdMateriaPrevia' => 2, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 8, 'CodigoMateria' => 'SIS-202', 'Nombre' => 'Física I', 'IdMateriaPrevia' => 2, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 9, 'CodigoMateria' => 'SIS-203', 'Nombre' => 'Programación I', 'IdMateriaPrevia' => 3, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 10, 'CodigoMateria' => 'SIS-204', 'Nombre' => 'Arquitectura de Computadoras', 'IdMateriaPrevia' => 4, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 11, 'CodigoMateria' => 'SIS-205', 'Nombre' => 'Algoritmos y Estructuras de Datos', 'IdMateriaPrevia' => 9, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 12, 'CodigoMateria' => 'SIS-206', 'Nombre' => 'Estadística Descriptiva', 'IdMateriaPrevia' => 2, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- TERCER SEMESTRE ---
            ['IdMateria' => 13, 'CodigoMateria' => 'SIS-301', 'Nombre' => 'Matemática III', 'IdMateriaPrevia' => 7, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 14, 'CodigoMateria' => 'SIS-302', 'Nombre' => 'Programación II', 'IdMateriaPrevia' => 9, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 15, 'CodigoMateria' => 'SIS-303', 'Nombre' => 'Programación Orientada a Objetos', 'IdMateriaPrevia' => 14, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 16, 'CodigoMateria' => 'SIS-304', 'Nombre' => 'Base de Datos I', 'IdMateriaPrevia' => 9, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 17, 'CodigoMateria' => 'SIS-305', 'Nombre' => 'Sistemas Operativos I', 'IdMateriaPrevia' => 10, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 18, 'CodigoMateria' => 'SIS-306', 'Nombre' => 'Física II', 'IdMateriaPrevia' => 8, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- CUARTO SEMESTRE ---
            ['IdMateria' => 19, 'CodigoMateria' => 'SIS-401', 'Nombre' => 'Base de Datos II', 'IdMateriaPrevia' => 16, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 20, 'CodigoMateria' => 'SIS-402', 'Nombre' => 'Redes I', 'IdMateriaPrevia' => 10, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 21, 'CodigoMateria' => 'SIS-403', 'Nombre' => 'Sistemas Operativos II', 'IdMateriaPrevia' => 17, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 22, 'CodigoMateria' => 'SIS-404', 'Nombre' => 'Ingeniería de Software I', 'IdMateriaPrevia' => 15, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 23, 'CodigoMateria' => 'SIS-405', 'Nombre' => 'Desarrollo Web I', 'IdMateriaPrevia' => 14, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 24, 'CodigoMateria' => 'SIS-406', 'Nombre' => 'Probabilidad y Estadística', 'IdMateriaPrevia' => 12, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- QUINTO SEMESTRE ---
            ['IdMateria' => 25, 'CodigoMateria' => 'SIS-501', 'Nombre' => 'Redes II', 'IdMateriaPrevia' => 20, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 26, 'CodigoMateria' => 'SIS-502', 'Nombre' => 'Ingeniería de Software II', 'IdMateriaPrevia' => 22, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 27, 'CodigoMateria' => 'SIS-503', 'Nombre' => 'Desarrollo Web II', 'IdMateriaPrevia' => 23, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 28, 'CodigoMateria' => 'SIS-504', 'Nombre' => 'Desarrollo Móvil I', 'IdMateriaPrevia' => 15, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 29, 'CodigoMateria' => 'SIS-505', 'Nombre' => 'Base de Datos Avanzada', 'IdMateriaPrevia' => 19, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 30, 'CodigoMateria' => 'SIS-506', 'Nombre' => 'Seguridad Informática I', 'IdMateriaPrevia' => 20, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- SEXTO SEMESTRE ---
            ['IdMateria' => 31, 'CodigoMateria' => 'SIS-601', 'Nombre' => 'Arquitectura de Software', 'IdMateriaPrevia' => 26, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 32, 'CodigoMateria' => 'SIS-602', 'Nombre' => 'Desarrollo Móvil II', 'IdMateriaPrevia' => 28, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 33, 'CodigoMateria' => 'SIS-603', 'Nombre' => 'Seguridad Informática II', 'IdMateriaPrevia' => 30, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 34, 'CodigoMateria' => 'SIS-604', 'Nombre' => 'Administración de Servidores', 'IdMateriaPrevia' => 21, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 35, 'CodigoMateria' => 'SIS-605', 'Nombre' => 'Inteligencia Artificial I', 'IdMateriaPrevia' => 13, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 36, 'CodigoMateria' => 'SIS-606', 'Nombre' => 'Gestión de Proyectos de Software', 'IdMateriaPrevia' => 26, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- SÉPTIMO SEMESTRE ---
            ['IdMateria' => 37, 'CodigoMateria' => 'SIS-701', 'Nombre' => 'Inteligencia Artificial II', 'IdMateriaPrevia' => 35, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 38, 'CodigoMateria' => 'SIS-702', 'Nombre' => 'Computación en la Nube', 'IdMateriaPrevia' => 34, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 39, 'CodigoMateria' => 'SIS-703', 'Nombre' => 'Auditoría de Sistemas', 'IdMateriaPrevia' => 33, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 40, 'CodigoMateria' => 'SIS-704', 'Nombre' => 'Minería de Datos', 'IdMateriaPrevia' => 29, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 41, 'CodigoMateria' => 'SIS-705', 'Nombre' => 'Redes Avanzadas', 'IdMateriaPrevia' => 25, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 42, 'CodigoMateria' => 'SIS-706', 'Nombre' => 'Sistemas Distribuidos', 'IdMateriaPrevia' => 31, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- OCTAVO SEMESTRE ---
            ['IdMateria' => 43, 'CodigoMateria' => 'SIS-801', 'Nombre' => 'Desarrollo de APIs y Microservicios', 'IdMateriaPrevia' => 31, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 44, 'CodigoMateria' => 'SIS-802', 'Nombre' => 'Ciberseguridad', 'IdMateriaPrevia' => 33, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 45, 'CodigoMateria' => 'SIS-803', 'Nombre' => 'Business Intelligence', 'IdMateriaPrevia' => 40, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 46, 'CodigoMateria' => 'SIS-804', 'Nombre' => 'Internet de las Cosas (IoT)', 'IdMateriaPrevia' => 41, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 47, 'CodigoMateria' => 'SIS-805', 'Nombre' => 'Gestión de Calidad de Software', 'IdMateriaPrevia' => 36, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 48, 'CodigoMateria' => 'SIS-806', 'Nombre' => 'Taller de Investigación', 'IdMateriaPrevia' => 6, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // --- NOVENO SEMESTRE ---
            ['IdMateria' => 49, 'CodigoMateria' => 'SIS-901', 'Nombre' => 'Proyecto de Grado', 'IdMateriaPrevia' => 48, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 50, 'CodigoMateria' => 'SIS-902', 'Nombre' => 'Práctica Profesional', 'IdMateriaPrevia' => 48, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 51, 'CodigoMateria' => 'SIS-903', 'Nombre' => 'Ética Profesional', 'IdMateriaPrevia' => 1, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 52, 'CodigoMateria' => 'SIS-904', 'Nombre' => 'Gestión de Innovación Tecnológica', 'IdMateriaPrevia' => 36, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 53, 'CodigoMateria' => 'SIS-905', 'Nombre' => 'Seminario de Actualización Tecnológica', 'IdMateriaPrevia' => 48, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 54, 'CodigoMateria' => 'SIS-906', 'Nombre' => 'Formulación y Evaluación de Proyectos', 'IdMateriaPrevia' => 36, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // =========================================================================
            // CONTADURÍA PÚBLICA (36 materias / IDs: 55 - 90) -> 6 Semestres x 6 Materias
            // =========================================================================
            // --- PRIMER SEMESTRE ---
            ['IdMateria' => 55, 'CodigoMateria' => 'CON-101', 'Nombre' => 'Contabilidad Básica I', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 56, 'CodigoMateria' => 'CON-102', 'Nombre' => 'Documentos Mercantiles', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 57, 'CodigoMateria' => 'CON-103', 'Nombre' => 'Administración General', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 58, 'CodigoMateria' => 'CON-104', 'Nombre' => 'Matemática Financiera I', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 59, 'CodigoMateria' => 'CON-105', 'Nombre' => 'Introducción al Derecho Comercial', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 60, 'CodigoMateria' => 'CON-106', 'Nombre' => 'Técnicas de Expresión e Investigación', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- SEGUNDO SEMESTRE ---
            ['IdMateria' => 61, 'CodigoMateria' => 'CON-201', 'Nombre' => 'Contabilidad Intermedia II', 'IdMateriaPrevia' => 55, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 62, 'CodigoMateria' => 'CON-202', 'Nombre' => 'Matemática Financiera II', 'IdMateriaPrevia' => 58, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 63, 'CodigoMateria' => 'CON-203', 'Nombre' => 'Legislación Laboral y Seguridad Social', 'IdMateriaPrevia' => 59, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 64, 'CodigoMateria' => 'CON-204', 'Nombre' => 'Microeconomía Aplicada', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 65, 'CodigoMateria' => 'CON-205', 'Nombre' => 'Estadística General', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 66, 'CodigoMateria' => 'CON-206', 'Nombre' => 'Informática Aplicada a la Contabilidad', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- TERCER SEMESTRE ---
            ['IdMateria' => 67, 'CodigoMateria' => 'CON-301', 'Nombre' => 'Contabilidad de Costos I', 'IdMateriaPrevia' => 61, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 68, 'CodigoMateria' => 'CON-302', 'Nombre' => 'Macroeconomía', 'IdMateriaPrevia' => 64, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 69, 'CodigoMateria' => 'CON-303', 'Nombre' => 'Derecho Tributario', 'IdMateriaPrevia' => 63, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 70, 'CodigoMateria' => 'CON-304', 'Nombre' => 'Estadística Inferencial', 'IdMateriaPrevia' => 65, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 71, 'CodigoMateria' => 'CON-305', 'Nombre' => 'Sistemas de Información Contable', 'IdMateriaPrevia' => 66, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 72, 'CodigoMateria' => 'CON-306', 'Nombre' => 'Administración Financiera I', 'IdMateriaPrevia' => 62, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- CUARTO SEMESTRE ---
            ['IdMateria' => 73, 'CodigoMateria' => 'CON-401', 'Nombre' => 'Contabilidad de Costos II', 'IdMateriaPrevia' => 67, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 74, 'CodigoMateria' => 'CON-402', 'Nombre' => 'Régimen Tributario Nacional', 'IdMateriaPrevia' => 69, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 75, 'CodigoMateria' => 'CON-403', 'Nombre' => 'Administración Financiera II', 'IdMateriaPrevia' => 72, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 76, 'CodigoMateria' => 'CON-404', 'Nombre' => 'Contabilidad de Sociedades', 'IdMateriaPrevia' => 61, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 77, 'CodigoMateria' => 'CON-405', 'Nombre' => 'Presupuestos y Control de Gestión', 'IdMateriaPrevia' => 67, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 78, 'CodigoMateria' => 'CON-406', 'Nombre' => 'Metodología de la Investigación Contable', 'IdMateriaPrevia' => 60, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- QUINTO SEMESTRE ---
            ['IdMateria' => 79, 'CodigoMateria' => 'CON-501', 'Nombre' => 'Análisis de Estados Financieros', 'IdMateriaPrevia' => 76, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 80, 'CodigoMateria' => 'CON-502', 'Nombre' => 'Auditoría Financiera I', 'IdMateriaPrevia' => 73, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 81, 'CodigoMateria' => 'CON-503', 'Nombre' => 'Contabilidad Gubernamental', 'IdMateriaPrevia' => 76, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 82, 'CodigoMateria' => 'CON-504', 'Nombre' => 'Preparación y Evaluación de Proyectos', 'IdMateriaPrevia' => 75, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 83, 'CodigoMateria' => 'CON-505', 'Nombre' => 'Contabilidad de Entidades Financieras', 'IdMateriaPrevia' => 76, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 84, 'CodigoMateria' => 'CON-506', 'Nombre' => 'Ética y Deontología Profesional', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- SEXTO SEMESTRE ---
            ['IdMateria' => 85, 'CodigoMateria' => 'CON-601', 'Nombre' => 'Auditoría Financiera II', 'IdMateriaPrevia' => 80, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 86, 'CodigoMateria' => 'CON-602', 'Nombre' => 'Auditoría Gubernamental', 'IdMateriaPrevia' => 81, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 87, 'CodigoMateria' => 'CON-603', 'Nombre' => 'Auditoría Tributaria', 'IdMateriaPrevia' => 74, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 88, 'CodigoMateria' => 'CON-604', 'Nombre' => 'Auditoría Forense', 'IdMateriaPrevia' => 85, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 89, 'CodigoMateria' => 'CON-605', 'Nombre' => 'Taller de Práctica Profesional Contable', 'IdMateriaPrevia' => 79, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 90, 'CodigoMateria' => 'CON-606', 'Nombre' => 'Modalidad de Grado / Tesis', 'IdMateriaPrevia' => 78, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // =========================================================================
            // DISEÑO GRÁFICO (36 materias / IDs: 91 - 126) -> 6 Semestres x 6 Materias
            // =========================================================================
            // --- PRIMER SEMESTRE ---
            ['IdMateria' => 91, 'CodigoMateria' => 'DG-101', 'Nombre' => 'Fundamentos del Diseño y Composición', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 92, 'CodigoMateria' => 'DG-102', 'Nombre' => 'Dibujo Artístico y Expresión Plástica', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 93, 'CodigoMateria' => 'DG-103', 'Nombre' => 'Historia del Arte y del Diseño', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 94, 'CodigoMateria' => 'DG-104', 'Nombre' => 'Geometría Descriptiva', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 95, 'CodigoMateria' => 'DG-105', 'Nombre' => 'Creatividad y Comunicación Visual', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 96, 'CodigoMateria' => 'DG-106', 'Nombre' => 'Taller de Redacción Creativa', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- SEGUNDO SEMESTRE ---
            ['IdMateria' => 97, 'CodigoMateria' => 'DG-201', 'Nombre' => 'Teoría y Psicología del Color', 'IdMateriaPrevia' => 91, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 98, 'CodigoMateria' => 'DG-202', 'Nombre' => 'Ilustración Digital I', 'IdMateriaPrevia' => 92, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 99, 'CodigoMateria' => 'DG-203', 'Nombre' => 'Tipografía I', 'IdMateriaPrevia' => 91, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 100, 'CodigoMateria' => 'DG-204', 'Nombre' => 'Diseño Vectorial Automático', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 101, 'CodigoMateria' => 'DG-205', 'Nombre' => 'Fotografía Básica y Composición', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 102, 'CodigoMateria' => 'DG-206', 'Nombre' => 'Semiótica de la Imagen', 'IdMateriaPrevia' => 95, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- TERCER SEMESTRE ---
            ['IdMateria' => 103, 'CodigoMateria' => 'DG-301', 'Nombre' => 'Diseño Editorial', 'IdMateriaPrevia' => 99, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 104, 'CodigoMateria' => 'DG-302', 'Nombre' => 'Ilustración Digital II', 'IdMateriaPrevia' => 98, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 105, 'CodigoMateria' => 'DG-303', 'Nombre' => 'Tipografía II', 'IdMateriaPrevia' => 99, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 106, 'CodigoMateria' => 'DG-304', 'Nombre' => 'Fotografía e Iluminación Publicitaria', 'IdMateriaPrevia' => 101, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 107, 'CodigoMateria' => 'DG-305', 'Nombre' => 'Diseño de Marcas e Identidad Visual', 'IdMateriaPrevia' => 97, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 108, 'CodigoMateria' => 'DG-306', 'Nombre' => 'Técnicas de Impresión y Pre-prensa', 'IdMateriaPrevia' => 100, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- CUARTO SEMESTRE ---
            ['IdMateria' => 109, 'CodigoMateria' => 'DG-401', 'Nombre' => 'Diseño Publicitario e Infografía', 'IdMateriaPrevia' => 103, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 110, 'CodigoMateria' => 'DG-402', 'Nombre' => 'Diseño de Empaques (Packaging)', 'IdMateriaPrevia' => 94, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 111, 'CodigoMateria' => 'DG-403', 'Nombre' => 'Edición Audiovisual y Video', 'IdMateriaPrevia' => 106, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 112, 'CodigoMateria' => 'DG-404', 'Nombre' => 'Branding y Estrategia de Diseño', 'IdMateriaPrevia' => 107, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 113, 'CodigoMateria' => 'DG-405', 'Nombre' => 'Introducción al Diseño Web', 'IdMateriaPrevia' => 100, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 114, 'CodigoMateria' => 'DG-406', 'Nombre' => 'Metodología para Proyectos de Diseño', 'IdMateriaPrevia' => 96, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- QUINTO SEMESTRE ---
            ['IdMateria' => 115, 'CodigoMateria' => 'DG-501', 'Nombre' => 'Animación 2D y Motion Graphics', 'IdMateriaPrevia' => 111, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 116, 'CodigoMateria' => 'DG-502', 'Nombre' => 'Diseño UX/UI Avanzado', 'IdMateriaPrevia' => 113, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 117, 'CodigoMateria' => 'DG-503', 'Nombre' => 'Modelado Digital 3D', 'IdMateriaPrevia' => 104, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 118, 'CodigoMateria' => 'DG-504', 'Nombre' => 'Campañas de Comunicación Visual', 'IdMateriaPrevia' => 109, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 119, 'CodigoMateria' => 'DG-505', 'Nombre' => 'Costos y Presupuestos de Diseño', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 120, 'CodigoMateria' => 'DG-506', 'Nombre' => 'Ética y Legislación del Derecho de Autor', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- SEXTO SEMESTRE ---
            ['IdMateria' => 121, 'CodigoMateria' => 'DG-601', 'Nombre' => 'Animación y Render 3D', 'IdMateriaPrevia' => 117, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 122, 'CodigoMateria' => 'DG-602', 'Nombre' => 'Diseño Multimedia e Interactivo', 'IdMateriaPrevia' => 116, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 123, 'CodigoMateria' => 'DG-603', 'Nombre' => 'Gestión y Dirección de Agencias de Diseño', 'IdMateriaPrevia' => 119, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 124, 'CodigoMateria' => 'DG-604', 'Nombre' => 'Portafolio Profesional y Personal Branding', 'IdMateriaPrevia' => 112, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 125, 'CodigoMateria' => 'DG-605', 'Nombre' => 'Taller de Práctica Profesional Guiada', 'IdMateriaPrevia' => 118, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 126, 'CodigoMateria' => 'DG-606', 'Nombre' => 'Proyecto Final de Grado', 'IdMateriaPrevia' => 114, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // =========================================================================
            // ADMINISTRACIÓN DE EMPRESAS (36 materias / IDs: 127 - 162) -> 6 Semestres x 6 Materias
            // =========================================================================
            // --- PRIMER SEMESTRE ---
            ['IdMateria' => 127, 'CodigoMateria' => 'ADM-101', 'Nombre' => 'Introducción a la Administración', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 128, 'CodigoMateria' => 'ADM-102', 'Nombre' => 'Matemática para Administradores I', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 129, 'CodigoMateria' => 'ADM-103', 'Nombre' => 'Contabilidad General de Empresas', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 130, 'CodigoMateria' => 'ADM-104', 'Nombre' => 'Historia Económica del Entorno', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 131, 'CodigoMateria' => 'ADM-105', 'Nombre' => 'Sociología de las Organizaciones', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 132, 'CodigoMateria' => 'ADM-106', 'Nombre' => 'Metodología del Trabajo Universitario', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- SEGUNDO SEMESTRE ---
            ['IdMateria' => 133, 'CodigoMateria' => 'ADM-201', 'Nombre' => 'Procesos Administrativos', 'IdMateriaPrevia' => 127, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 134, 'CodigoMateria' => 'ADM-202', 'Nombre' => 'Matemática para Administradores II', 'IdMateriaPrevia' => 128, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 135, 'CodigoMateria' => 'ADM-203', 'Nombre' => 'Microeconomía Organizacional', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 136, 'CodigoMateria' => 'ADM-204', 'Nombre' => 'Derecho Empresarial y Corporativo', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 137, 'CodigoMateria' => 'ADM-205', 'Nombre' => 'Estadística Aplicada al Negocio', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 138, 'CodigoMateria' => 'ADM-206', 'Nombre' => 'Sistemas de Información Empresarial', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- TERCER SEMESTRE ---
            ['IdMateria' => 139, 'CodigoMateria' => 'ADM-301', 'Nombre' => 'Organización y Métodos', 'IdMateriaPrevia' => 133, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 140, 'CodigoMateria' => 'ADM-302', 'Nombre' => 'Macroeconomía Aplicada', 'IdMateriaPrevia' => 135, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 141, 'CodigoMateria' => 'ADM-303', 'Nombre' => 'Contabilidad de Costos Gerenciales', 'IdMateriaPrevia' => 129, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 142, 'CodigoMateria' => 'ADM-304', 'Nombre' => 'Derecho Laboral Comercial', 'IdMateriaPrevia' => 136, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 143, 'CodigoMateria' => 'ADM-305', 'Nombre' => 'Estadística Inferencial para Negocios', 'IdMateriaPrevia' => 137, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 144, 'CodigoMateria' => 'ADM-306', 'Nombre' => 'Administración de Recursos Humanos I', 'IdMateriaPrevia' => 131, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- CUARTO SEMESTRE ---
            ['IdMateria' => 145, 'CodigoMateria' => 'ADM-401', 'Nombre' => 'Administración de Recursos Humanos II', 'IdMateriaPrevia' => 144, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 146, 'CodigoMateria' => 'ADM-402', 'Nombre' => 'Fundamentos de Marketing', 'IdMateriaPrevia' => 133, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 147, 'CodigoMateria' => 'ADM-403', 'Nombre' => 'Administración Financiera I', 'IdMateriaPrevia' => 141, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 148, 'CodigoMateria' => 'ADM-404', 'Nombre' => 'Administración de la Producción y Operaciones I', 'IdMateriaPrevia' => 139, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 149, 'CodigoMateria' => 'ADM-405', 'Nombre' => 'Investigación de Operaciones I', 'IdMateriaPrevia' => 134, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 150, 'CodigoMateria' => 'ADM-406', 'Nombre' => 'Metodología de la Investigación Empresarial', 'IdMateriaPrevia' => 132, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- QUINTO SEMESTRE ---
            ['IdMateria' => 151, 'CodigoMateria' => 'ADM-501', 'Nombre' => 'Comportamiento y Desarrollo Organizacional', 'IdMateriaPrevia' => 145, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 152, 'CodigoMateria' => 'ADM-502', 'Nombre' => 'Investigación de Mercados', 'IdMateriaPrevia' => 146, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 153, 'CodigoMateria' => 'ADM-503', 'Nombre' => 'Administración Financiera II', 'IdMateriaPrevia' => 147, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 154, 'CodigoMateria' => 'ADM-504', 'Nombre' => 'Administración de la Producción II', 'IdMateriaPrevia' => 148, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 155, 'CodigoMateria' => 'ADM-505', 'Nombre' => 'Preparación y Formulación de Proyectos', 'IdMateriaPrevia' => 147, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 156, 'CodigoMateria' => 'ADM-506', 'Nombre' => 'Comercio Exterior y Negocios Internacionales', 'IdMateriaPrevia' => 140, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- SEXTO SEMESTRE ---
            ['IdMateria' => 157, 'CodigoMateria' => 'ADM-601', 'Nombre' => 'Dirección Estratégica Corporativa', 'IdMateriaPrevia' => 151, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 158, 'CodigoMateria' => 'ADM-602', 'Nombre' => 'Gerencia de Marketing', 'IdMateriaPrevia' => 152, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 159, 'CodigoMateria' => 'ADM-603', 'Nombre' => 'Evaluación y Auditoría de Proyectos', 'IdMateriaPrevia' => 155, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 160, 'CodigoMateria' => 'ADM-604', 'Nombre' => 'Plan de Negocios y Emprendimiento', 'IdMateriaPrevia' => 153, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 161, 'CodigoMateria' => 'ADM-605', 'Nombre' => 'Taller de Prácticas Profesionales Empresariales', 'IdMateriaPrevia' => 151, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 162, 'CodigoMateria' => 'ADM-606', 'Nombre' => 'Proyecto Final de Grado / Tesis', 'IdMateriaPrevia' => 150, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],

            // =========================================================================
            // DERECHO (36 materias / IDs: 163 - 198) -> 6 Semestres x 6 Materias
            // =========================================================================
            // --- PRIMER SEMESTRE ---
            ['IdMateria' => 163, 'CodigoMateria' => 'DER-101', 'Nombre' => 'Introducción al Derecho y Ciencias Jurídicas', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 164, 'CodigoMateria' => 'DER-102', 'Nombre' => 'Derecho Romano e Historia Jurídica', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 165, 'CodigoMateria' => 'DER-103', 'Nombre' => 'Derecho Político y Teoría del Estado', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 166, 'CodigoMateria' => 'DER-104', 'Nombre' => 'Filosofía Jurídica y Deontología', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 167, 'CodigoMateria' => 'DER-105', 'Nombre' => 'Oratoria y Argumentación Jurídica', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 168, 'CodigoMateria' => 'DER-106', 'Nombre' => 'Sociología Jurídica y Pluralismo', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- SEGUNDO SEMESTRE ---
            ['IdMateria' => 169, 'CodigoMateria' => 'DER-201', 'Nombre' => 'Derecho Constitucional', 'IdMateriaPrevia' => 165, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 170, 'CodigoMateria' => 'DER-202', 'Nombre' => 'Derecho Civil I (Personas y Derechos Reales)', 'IdMateriaPrevia' => 163, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 171, 'CodigoMateria' => 'DER-203', 'Nombre' => 'Derecho Penal I (Parte General)', 'IdMateriaPrevia' => 163, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 172, 'CodigoMateria' => 'DER-204', 'Nombre' => 'Derecho Internacional Público', 'IdMateriaPrevia' => 165, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 173, 'CodigoMateria' => 'DER-205', 'Nombre' => 'Derecho Administrativo I', 'IdMateriaPrevia' => 163, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 174, 'CodigoMateria' => 'DER-206', 'Nombre' => 'Criminología y Política Criminal', 'IdMateriaPrevia' => 168, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- TERCER SEMESTRE ---
            ['IdMateria' => 175, 'CodigoMateria' => 'DER-301', 'Nombre' => 'Derecho Civil II (Obligaciones y Contratos)', 'IdMateriaPrevia' => 170, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 176, 'CodigoMateria' => 'DER-302', 'Nombre' => 'Derecho Penal II (Delitos y Penas)', 'IdMateriaPrevia' => 171, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 177, 'CodigoMateria' => 'DER-303', 'Nombre' => 'Derecho Orgánico Constitucional y de Amparos', 'IdMateriaPrevia' => 169, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 178, 'CodigoMateria' => 'DER-304', 'Nombre' => 'Derecho Administrativo II y Procedimientos', 'IdMateriaPrevia' => 173, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 179, 'CodigoMateria' => 'DER-305', 'Nombre' => 'Derecho de Familia y del Menor', 'IdMateriaPrevia' => 170, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 180, 'CodigoMateria' => 'DER-306', 'Nombre' => 'Metodología de la Investigación Jurídica', 'IdMateriaPrevia' => 166, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- CUARTO SEMESTRE ---
            ['IdMateria' => 181, 'CodigoMateria' => 'DER-401', 'Nombre' => 'Derecho Procesal Civil I', 'IdMateriaPrevia' => 175, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 182, 'CodigoMateria' => 'DER-402', 'Nombre' => 'Derecho Procesal Penal I', 'IdMateriaPrevia' => 176, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 183, 'CodigoMateria' => 'DER-403', 'Nombre' => 'Derecho Civil III (Sucesiones)', 'IdMateriaPrevia' => 175, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 184, 'CodigoMateria' => 'DER-404', 'Nombre' => 'Derecho Comercial y de Sociedades', 'IdMateriaPrevia' => 175, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 185, 'CodigoMateria' => 'DER-405', 'Nombre' => 'Derecho Laboral y de la Seguridad Social', 'IdMateriaPrevia' => 178, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 186, 'CodigoMateria' => 'DER-406', 'Nombre' => 'Derecho Minero e Hidrocarburos', 'IdMateriaPrevia' => null, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- QUINTO SEMESTRE ---
            ['IdMateria' => 187, 'CodigoMateria' => 'DER-501', 'Nombre' => 'Derecho Procesal Civil II', 'IdMateriaPrevia' => 181, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 188, 'CodigoMateria' => 'DER-502', 'Nombre' => 'Derecho Procesal Penal II', 'IdMateriaPrevia' => 182, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 189, 'CodigoMateria' => 'DER-503', 'Nombre' => 'Derecho Internacional Privado', 'IdMateriaPrevia' => 172, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 190, 'CodigoMateria' => 'DER-504', 'Nombre' => 'Derecho Procesal Laboral', 'IdMateriaPrevia' => 185, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 191, 'CodigoMateria' => 'DER-505', 'Nombre' => 'Derecho Financiero y Tributario', 'IdMateriaPrevia' => 184, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 192, 'CodigoMateria' => 'DER-506', 'Nombre' => 'Medicina Legal y Forense', 'IdMateriaPrevia' => 176, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            // --- SEXTO SEMESTRE ---
            ['IdMateria' => 193, 'CodigoMateria' => 'DER-601', 'Nombre' => 'Práctica Forense Civil y Litigación', 'IdMateriaPrevia' => 187, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 194, 'CodigoMateria' => 'DER-602', 'Nombre' => 'Práctica Forense Penal y Oralidad', 'IdMateriaPrevia' => 188, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 195, 'CodigoMateria' => 'DER-603', 'Nombre' => 'Mecanismos Alternativos de Resolución de Conflictos', 'IdMateriaPrevia' => 167, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 196, 'CodigoMateria' => 'DER-604', 'Nombre' => 'Derecho Ambiental y Autonómico', 'IdMateriaPrevia' => 169, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 197, 'CodigoMateria' => 'DER-605', 'Nombre' => 'Taller de Práctica Profesional Clínica Jurídica', 'IdMateriaPrevia' => 193, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
            ['IdMateria' => 198, 'CodigoMateria' => 'DER-606', 'Nombre' => 'Modalidad de Grado / Tesis Final', 'IdMateriaPrevia' => 180, 'Descripcion' => null, 'FechaRegistro' => now(), 'Estado' => true],
        ]);
    }
}