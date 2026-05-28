<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('IdUsuario'); // INT AUTO_INCREMENT PRIMARY KEY
            
            // Llave foránea hacia la tabla Roles (Obligatoria)
            $table->foreignId('IdRol')->constrained('roles', 'IdRol');

            $table->string('Nombre1', 50); // VARCHAR(50) NOT NULL
            $table->string('Nombre2', 50)->nullable(); // VARCHAR(50) (Opcional)
            $table->string('Apellido1', 50); // VARCHAR(50) NOT NULL
            $table->string('Apellido2', 50)->nullable(); // VARCHAR(50) (Opcional)
            
            $table->integer('CI')->unique(); // INT UNIQUE NOT NULL
            $table->integer('Telefono')->nullable(); // INT (Opcional)
            $table->string('Correo', 100)->unique(); // VARCHAR(100) UNIQUE NOT NULL
            $table->string('Contrasena', 255); // VARCHAR(255) NOT NULL
            
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            
            // Llave foránea hacia la tabla Carreras (Opcional)
            $table->foreignId('IdCarrera') ->nullable() ->constrained('carreras', 'IdCarrera');

            $table->integer('Semestre')->nullable(); // INT NULL
            $table->boolean('Estado')->default(true); // BOOL DEFAULT TRUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
