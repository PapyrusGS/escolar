<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ModularEstudianteSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('usuarios')->where('Correo', 'marcos@gmail.com')->exists()) {
            $this->command?->warn('Modular student Marcos Garcia already exists, skipping.');
            return;
        }

        $hoy = Carbon::now();

        // 1. Crear usuario estudiante
        DB::table('usuarios')->insert([
            'IdRol' => 3,
            'Nombre1' => 'Marcos',
            'Nombre2' => null,
            'Apellido1' => 'Garcia',
            'Apellido2' => null,
            'CI' => 12345679,
            'Telefono' => null,
            'Correo' => 'marcos@gmail.com',
            'Contrasena' => Hash::make('123456'),
            'FechaRegistro' => now(),
            'Estado' => true,
        ]);

        // 2. Asignar carrera y modalidad (Modular = 1)
        DB::table('EstudianteCarrera')->insert([
            'IdUsuario' => 17,
            'IdCarrera' => 1,
            'IdModalidad' => 1,
            'FechaRegistro' => now(),
            'Estado' => true,
        ]);

        // 3. Cursos-materias ya finalizados (primer semestre, fechas pasadas)
        $cursosMaterias = [
            ['IdCurso' => 5,  'IdMateria' => 1, 'IdDocente' => 5,  'IdTurno' => 1],
            ['IdCurso' => 6,  'IdMateria' => 2, 'IdDocente' => 5,  'IdTurno' => 2],
            ['IdCurso' => 7,  'IdMateria' => 3, 'IdDocente' => 15, 'IdTurno' => 1],
            ['IdCurso' => 8,  'IdMateria' => 4, 'IdDocente' => 15, 'IdTurno' => 3],
            ['IdCurso' => 9,  'IdMateria' => 5, 'IdDocente' => 16, 'IdTurno' => 1],
            ['IdCurso' => 10, 'IdMateria' => 6, 'IdDocente' => 16, 'IdTurno' => 2],
        ];

        $idCursoMaterias = [];
        foreach ($cursosMaterias as $cm) {
            DB::table('cursos_materias')->insert([
                'IdCurso' => $cm['IdCurso'],
                'IdMateria' => $cm['IdMateria'],
                'IdDocente' => $cm['IdDocente'],
                'IdTurno' => $cm['IdTurno'],
                'FechaInicio' => $hoy->copy()->subDays(60)->toDateString(),
                'FechaFin' => $hoy->copy()->subDays(30)->toDateString(),
                'MaxInscritos' => 30,
                'Inscritos' => 1,
                'FechaRegistro' => now(),
                'Estado' => true,
            ]);
            $idCursoMaterias[] = DB::getPdo()->lastInsertId();
        }

        // 4. Inscripciones del estudiante
        $idEstudiante = DB::table('EstudianteCarrera')
            ->where('IdUsuario', 17)
            ->value('IdEstudianteCarrera');

        $inscripcionIds = [];
        foreach ($idCursoMaterias as $idCursoMateria) {
            DB::table('inscripciones')->insert([
                'IdEstudiante' => $idEstudiante,
                'IdCursoMateria' => $idCursoMateria,
                'Fecha' => $hoy->copy()->subDays(45)->toDateString(),
                'Estado' => true,
                'Aprobado' => 0,
            ]);
            $inscripcionIds[] = DB::getPdo()->lastInsertId();
        }

        // 5. Notas: 5 aprobadas (>= 51), 1 reprobada — Matemática I (SIS-102)
        $notas = [
            ['IdInscripcion' => $inscripcionIds[0], 'Nota' => 80.00], // SIS-101 ✅
            ['IdInscripcion' => $inscripcionIds[1], 'Nota' => 35.00], // SIS-102 ❌ REPROBADA
            ['IdInscripcion' => $inscripcionIds[2], 'Nota' => 90.00], // SIS-103 ✅
            ['IdInscripcion' => $inscripcionIds[3], 'Nota' => 85.00], // SIS-104 ✅
            ['IdInscripcion' => $inscripcionIds[4], 'Nota' => 70.00], // SIS-105 ✅
            ['IdInscripcion' => $inscripcionIds[5], 'Nota' => 75.00], // SIS-106 ✅
        ];

        foreach ($notas as $nota) {
            DB::table('notas')->insert([
                'IdInscripcion' => $nota['IdInscripcion'],
                'Nota' => $nota['Nota'],
                'FechaRegistro' => $hoy->copy()->subDays(30)->toDateString(),
                'Estado' => true,
            ]);
        }

        $this->command?->info('ModularEstudianteSeeder: Marcos Garcia (modular, Matematica I reprobada).');
    }
}
