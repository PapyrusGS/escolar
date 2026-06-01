<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('turnos')->insert([
            // --- Tus 3 turnos originales (Sin Domingos) ---
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

            // --- 15 Turnos Nuevos (Días Variados y 0 Domingos) ---
            [
                'Nombre' => 'Madrugada L-V',
                'HoraInicio' => '00:00:00',
                'HoraFin' => '06:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Mañana Extendido M-J-V',
                'HoraInicio' => '07:00:00',
                'HoraFin' => '15:00:00',
                'Lun' => false, 'Mar' => true, 'Mie' => false, 'Jue' => true, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Tarde Extendido L-M-X',
                'HoraInicio' => '14:00:00',
                'HoraFin' => '22:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => true, 'Jue' => false, 'Vie' => false,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Sábados Mañana',
                'HoraInicio' => '08:00:00',
                'HoraFin' => '14:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => false, 'Vie' => false,
                'Sab' => true, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Sábados Tarde',
                'HoraInicio' => '14:00:00',
                'HoraFin' => '20:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => false, 'Vie' => false,
                'Sab' => true, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Viernes y Sábados Noche',
                'HoraInicio' => '20:00:00',
                'HoraFin' => '02:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => false, 'Vie' => true,
                'Sab' => true, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Intermedio L-X-V',
                'HoraInicio' => '10:00:00',
                'HoraFin' => '18:00:00',
                'Lun' => true, 'Mar' => false, 'Mie' => true, 'Jue' => false, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Intermedio M-J-S',
                'HoraInicio' => '11:00:00',
                'HoraFin' => '19:00:00',
                'Lun' => false, 'Mar' => true, 'Mie' => false, 'Jue' => true, 'Vie' => false,
                'Sab' => true, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Inicio de Semana (L-M)',
                'HoraInicio' => '08:00:00',
                'HoraFin' => '16:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => false, 'Jue' => false, 'Vie' => false,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Mitad de Semana (X-J)',
                'HoraInicio' => '12:00:00',
                'HoraFin' => '20:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => true, 'Jue' => true, 'Vie' => false,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Fin de Semana Laboral (V-S)',
                'HoraInicio' => '09:00:00',
                'HoraFin' => '17:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => false, 'Vie' => true,
                'Sab' => true, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Guardia Nocturna Alterna',
                'HoraInicio' => '22:00:00',
                'HoraFin' => '06:00:00',
                'Lun' => true, 'Mar' => false, 'Mie' => true, 'Jue' => false, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Soporte Remoto J-V-S',
                'HoraInicio' => '16:00:00',
                'HoraFin' => '20:00:00',
                'Lun' => false, 'Mar' => false, 'Mie' => false, 'Jue' => true, 'Vie' => true,
                'Sab' => true, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Limpieza Temprana L-M-V',
                'HoraInicio' => '05:00:00',
                'HoraFin' => '09:00:00',
                'Lun' => true, 'Mar' => true, 'Mie' => false, 'Jue' => false, 'Vie' => true,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
            [
                'Nombre' => 'Refuerzo Técnico M-X-J',
                'HoraInicio' => '09:00:00',
                'HoraFin' => '15:00:00',
                'Lun' => false, 'Mar' => true, 'Mie' => true, 'Jue' => true, 'Vie' => false,
                'Sab' => false, 'Dom' => false,
                'FechaRegistro' => now(),
                'Estado' => true
            ],
        ]);
    }
}

