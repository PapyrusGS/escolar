<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('turnos')->insert([
            // --- Tus 3 turnos originales ---
            [
                'Nombre' => 'Mañana',
                'HoraInicio' => '08:00:00',
                'HoraFin' => '12:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Tarde',
                'HoraInicio' => '13:00:00',
                'HoraFin' => '17:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Noche',
                'HoraInicio' => '18:00:00',
                'HoraFin' => '22:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],

            // --- 15 Turnos Nuevos ---
            [
                'Nombre' => 'Madrugada',
                'HoraInicio' => '00:00:00',
                'HoraFin' => '06:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Mañana Extendido',
                'HoraInicio' => '07:00:00',
                'HoraFin' => '15:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Tarde Extendido',
                'HoraInicio' => '14:00:00',
                'HoraFin' => '22:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Fin de Semana Mañana',
                'HoraInicio' => '08:00:00',
                'HoraFin' => '14:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => false, 'Vie' => false,
                'Sab' => true, 'Dom' => true,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Fin de Semana Tarde',
                'HoraInicio' => '14:00:00',
                'HoraFin' => '20:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => false, 'Vie' => false,
                'Sab' => true, 'Dom' => true,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Fin de Semana Noche',
                'HoraInicio' => '20:00:00',
                'HoraFin' => '02:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => false, 'Vie' => false,
                'Sab' => true, 'Dom' => true,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Intermedio Completo',
                'HoraInicio' => '10:00:00',
                'HoraFin' => '18:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Lunes a Miércoles Mañana',
                'HoraInicio' => '08:00:00',
                'HoraFin' => '16:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => false, 'Vie' => false,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Jueves y Viernes Tarde',
                'HoraInicio' => '12:00:00',
                'HoraFin' => '20:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Rotativo A',
                'HoraInicio' => '06:00:00',
                'HoraFin' => '14:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => false, 'Jue' => true, 'Vie' => false,
                'Sab' => true, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Rotativo B',
                'HoraInicio' => '14:00:00',
                'HoraFin' => '22:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => true, 'Jue' => false, 'Vie' => true,
                'Sab' => false, 'Dom' => true,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Guardia Nocturna Especial',
                'HoraInicio' => '22:00:00',
                'HoraFin' => '06:00:00',
                'Lun' => true, 'Mar' => false, 'Mie' => true, 'Jue' => false, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Soporte Técnico Remoto',
                'HoraInicio' => '16:00:00',
                'HoraFin' => '20:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Limpieza y Mantenimiento',
                'HoraInicio' => '05:00:00',
                'HoraFin' => '09:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => true, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Solo Domingos',
                'HoraInicio' => '09:00:00',
                'HoraFin' => '18:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => false, 'Vie' => false,
                'Sab' => false, 'Dom' => true,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
        ]);
    }
}
