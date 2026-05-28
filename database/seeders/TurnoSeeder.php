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
                'Nombre' => 'Manana',
                'HoraInicio' => '07:00:00',
                'HoraFin' => '12:00:00',
                'Dias' => 'Lunes,Martes,Miercoles,Jueves,Viernes',
                'Estado' => true
            ],
            [
                'Nombre' => 'Tarde',
                'HoraInicio' => '13:00:00',
                'HoraFin' => '18:00:00',
                'Dias' => 'Lunes,Martes,Miercoles,Jueves,Viernes',
                'Estado' => true
            ],
            [
                'Nombre' => 'Noche',
                'HoraInicio' => '19:00:00',
                'HoraFin' => '22:00:00',
                'Dias' => 'Lunes,Martes,Miercoles,Jueves,Viernes',
                'Estado' => true
            ],
        ]);
    }
}
