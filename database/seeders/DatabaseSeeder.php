<?php

namespace Database\Seeders;

use App\Models\User;
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
            UsuarioSeeder::class,
            CursoSeeder::class,
            CursoMateriaSeeder::class,
            InscripcionSeeder::class,
            NotaSeeder::class,
            NotificacionSeeder::class,
        ]);
    }
}
