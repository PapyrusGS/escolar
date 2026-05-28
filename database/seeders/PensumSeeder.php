<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PensumSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pensum')->insert([
            ['IdCarrera' => 1, 'IdMateria' => 1, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 2, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 3, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 4, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 5, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 14, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 15, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 16, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 17, 'Estado' => true],
            ['IdCarrera' => 1, 'IdMateria' => 18, 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateria' => 6, 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateria' => 7, 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateria' => 8, 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateria' => 19, 'Estado' => true],
            ['IdCarrera' => 2, 'IdMateria' => 20, 'Estado' => true],
            ['IdCarrera' => 3, 'IdMateria' => 9, 'Estado' => true],
            ['IdCarrera' => 3, 'IdMateria' => 10, 'Estado' => true],
            ['IdCarrera' => 4, 'IdMateria' => 11, 'Estado' => true],
            ['IdCarrera' => 4, 'IdMateria' => 21, 'Estado' => true],
            ['IdCarrera' => 5, 'IdMateria' => 12, 'Estado' => true],
            ['IdCarrera' => 5, 'IdMateria' => 13, 'Estado' => true],
        ]);
    }
}
