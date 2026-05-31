<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            ModalidadSeeder::class,
            TurnoSeeder::class,
            CarreraSeeder::class,
            MateriaSeeder::class,
            PensumSeeder::class,
            CarreraMateriaPensumSeeder::class,
            UsuarioSeeder::class,
            EstudianteCarreraSeeder::class,
            CursoSeeder::class,
            CursoMateriaSeeder::class,
            InscripcionSeeder::class,
            NotaSeeder::class,
            NotificacionSeeder::class,
        ]);
    }
}
