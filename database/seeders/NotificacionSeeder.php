<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificacionSeeder extends Seeder
{
    public function run(): void
    {

        $usuarios = DB::table('usuarios')->get();

        foreach ($usuarios as $usuario) {
            $rolTexto = match ((int)$usuario->IdRol) {
                1 => 'Administrador',
                2 => 'Docente',
                3 => 'Estudiante',
                default => 'Usuario'
            };

            DB::table('notificaciones')->insert([
                'IdUsuario' => $usuario->IdUsuario,
                'Titulo' => '¡Bienvenido al Sistema!',
                'Contenido' => "Hola {$usuario->Nombre1}, tu cuenta con perfil de {$rolTexto} ha sido activada correctamente en el Sistema de Gestión Escolar. ¡Éxito en tus actividades!",
                'FechaEnvio' => now(),
                'FechaRegistro' => now(),
                'Estado' => true
            ]);
        }

        // 2. Insertar las notificaciones de prueba adicionales para casos específicos
        DB::table('notificaciones')->insert([
            ['IdUsuario' => 3,  'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito correctamente a Matemáticas I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 3,  'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito correctamente a Programación I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 6,  'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito correctamente a Matemáticas II', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 6,  'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito correctamente a Programación II', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 7,  'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito correctamente a Matemáticas I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 8,  'Titulo' => 'Nota registrada',           'Contenido' => 'Tu docente ha registrado una nota en Matemáticas II', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 8,  'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito correctamente a Contabilidad I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 9,  'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito a Contabilidad I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 9,  'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito a Álgebra Lineal', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 10, 'Titulo' => 'Nota registrada',           'Contenido' => 'Tu docente ha registrado una nota en Matemática Financiera', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 10, 'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito correctamente a Matemática Financiera', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 11, 'Titulo' => 'Bienvenido al sistema',     'Contenido' => 'Tu cuenta ha sido activada correctamente', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 12, 'Titulo' => 'Inscripción exitosa',       'Contenido' => 'Te has inscrito correctamente a Contabilidad I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 13, 'Titulo' => 'Nota registrada',           'Contenido' => 'Tu docente ha registrado una nota en Base de Datos I', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 14, 'Titulo' => 'Bienvenido al sistema',     'Contenido' => 'Tu cuenta ha sido activada correctamente', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 4,  'Titulo' => 'Nuevo usuario registrado',  'Contenido' => 'Se ha registrado un nuevo estudiante en el sistema', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 5,  'Titulo' => 'Nueva inscripción',         'Contenido' => 'Un estudiante se ha inscrito en tu materia', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
            ['IdUsuario' => 2,  'Titulo' => 'Nueva inscripción',         'Contenido' => 'Un estudiante se ha inscrito en tu materia', 'FechaEnvio' => now(), 'FechaRegistro' => now(), 'Estado' => true],
        ]);
    }
}
