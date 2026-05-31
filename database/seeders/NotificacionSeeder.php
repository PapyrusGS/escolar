<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notificaciones')->insert([
            ['IdUsuario' => 3,  'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito correctamente a Matematicas I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 3,  'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito correctamente a Programacion I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 6,  'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito correctamente a Matematicas II', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 6,  'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito correctamente a Programacion II', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 7,  'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito correctamente a Matematicas I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 8,  'Titulo' => 'Nota registrada',           'Contenido' => 'Tu docente ha registrado una nota en Matematicas II', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 8,  'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito correctamente a Contabilidad I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 9,  'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito a Contabilidad I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 9,  'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito a Algebra Lineal', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 10, 'Titulo' => 'Nota registrada',           'Contenido' => 'Tu docente ha registrado una nota en Matematica Financiera', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 10, 'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito correctamente a Matematica Financiera', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 11, 'Titulo' => 'Bienvenido al sistema',     'Contenido' => 'Tu cuenta ha sido activada correctamente', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 12, 'Titulo' => 'Inscripcion exitosa',       'Contenido' => 'Te has inscrito correctamente a Contabilidad I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 13, 'Titulo' => 'Nota registrada',           'Contenido' => 'Tu docente ha registrado una nota en Base de Datos I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 14, 'Titulo' => 'Bienvenido al sistema',     'Contenido' => 'Tu cuenta ha sido activada correctamente', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 4,  'Titulo' => 'Nuevo usuario registrado', 'Contenido' => 'Se ha registrado un nuevo estudiante en el sistema', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 5,  'Titulo' => 'Nueva inscripcion',        'Contenido' => 'Un estudiante se ha inscrito en tu materia', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 2,  'Titulo' => 'Nueva inscripcion',        'Contenido' => 'Un estudiante se ha inscrito en tu materia', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
        ]);
    }
}
