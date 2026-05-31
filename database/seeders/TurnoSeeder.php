<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('turnos')->insert([
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
        ]);
    }
}
