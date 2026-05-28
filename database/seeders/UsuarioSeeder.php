<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            ['IdRol' => 1, 'Nombre1' => 'Carlos', 'Nombre2' => null, 'Apellido1' => 'Mamani', 'Apellido2' => null, 'CI' => 12345678, 'Telefono' => null, 'Correo' => 'admin@sistema.edu', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => null, 'Semestre' => null, 'Estado' => true],
            ['IdRol' => 2, 'Nombre1' => 'Luis', 'Nombre2' => null, 'Apellido1' => 'Quispe', 'Apellido2' => null, 'CI' => 87654321, 'Telefono' => null, 'Correo' => 'docente@sistema.edu', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => null, 'Semestre' => null, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Maria', 'Nombre2' => null, 'Apellido1' => 'Flores', 'Apellido2' => null, 'CI' => 11223344, 'Telefono' => null, 'Correo' => 'estudiante@sistema.edu', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 1, 'Semestre' => 1, 'Estado' => true],
            ['IdRol' => 1, 'Nombre1' => 'Favio', 'Nombre2' => 'Andres', 'Apellido1' => 'Gutierrez', 'Apellido2' => 'Lopez', 'CI' => 55667788, 'Telefono' => 70123456, 'Correo' => 'favio@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => null, 'Semestre' => null, 'Estado' => true],
            ['IdRol' => 2, 'Nombre1' => 'Martin', 'Nombre2' => 'Eduardo', 'Apellido1' => 'Salazar', 'Apellido2' => 'Quispe', 'CI' => 99887766, 'Telefono' => 71234567, 'Correo' => 'martin@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => null, 'Semestre' => null, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Kike', 'Nombre2' => 'Enrique', 'Apellido1' => 'Torrez', 'Apellido2' => 'Mamani', 'CI' => 44556677, 'Telefono' => 72345678, 'Correo' => 'kike@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 1, 'Semestre' => 2, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Ana', 'Nombre2' => 'Maria', 'Apellido1' => 'Condori', 'Apellido2' => 'Huanca', 'CI' => 22334455, 'Telefono' => 73456789, 'Correo' => 'ana@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 1, 'Semestre' => 1, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Pedro', 'Nombre2' => 'Jose', 'Apellido1' => 'Vargas', 'Apellido2' => 'Choque', 'CI' => 33445566, 'Telefono' => 74567890, 'Correo' => 'pedro@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 1, 'Semestre' => 2, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Lucia', 'Nombre2' => 'Elena', 'Apellido1' => 'Rios', 'Apellido2' => 'Apaza', 'CI' => 44332211, 'Telefono' => 75678901, 'Correo' => 'lucia@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 2, 'Semestre' => 1, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Diego', 'Nombre2' => 'Fabian', 'Apellido1' => 'Mendoza', 'Apellido2' => 'Lima', 'CI' => 55443322, 'Telefono' => 76789012, 'Correo' => 'diego@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 2, 'Semestre' => 3, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Sofia', 'Nombre2' => 'Belen', 'Apellido1' => 'Castillo', 'Apellido2' => 'Romero', 'CI' => 66554433, 'Telefono' => 77890123, 'Correo' => 'sofia@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 1, 'Semestre' => 4, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Carlos', 'Nombre2' => 'Rodrigo', 'Apellido1' => 'Pinto', 'Apellido2' => 'Alarcon', 'CI' => 77665544, 'Telefono' => 78901234, 'Correo' => 'carlos2@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 2, 'Semestre' => 2, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Valeria', 'Nombre2' => 'Paola', 'Apellido1' => 'Espinoza', 'Apellido2' => 'Cruz', 'CI' => 88776655, 'Telefono' => 79012345, 'Correo' => 'valeria@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 1, 'Semestre' => 3, 'Estado' => true],
            ['IdRol' => 3, 'Nombre1' => 'Fernando', 'Nombre2' => 'Luis', 'Apellido1' => 'Medina', 'Apellido2' => 'Suarez', 'CI' => 99001122, 'Telefono' => 70112233, 'Correo' => 'fernando@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => 2, 'Semestre' => 1, 'Estado' => true],
            ['IdRol' => 2, 'Nombre1' => 'Patricia', 'Nombre2' => 'Rosa', 'Apellido1' => 'Montano', 'Apellido2' => 'Vega', 'CI' => 11009988, 'Telefono' => 71223344, 'Correo' => 'patricia@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => null, 'Semestre' => null, 'Estado' => true],
            ['IdRol' => 2, 'Nombre1' => 'Roberto', 'Nombre2' => 'Cesar', 'Apellido1' => 'Villena', 'Apellido2' => 'Tapia', 'CI' => 22110099, 'Telefono' => 72334455, 'Correo' => 'roberto@gmail.com', 'Contrasena' => Hash::make('123456'), 'IdCarrera' => null, 'Semestre' => null, 'Estado' => true],
        ]);
    }
}
