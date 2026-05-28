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
        Schema::create('cursos_materias', function (Blueprint $table) {
            $table->id('IdCursoMateria'); // INT AUTO_INCREMENT PRIMARY KEY
            
            // Llaves foráneas
            $table->foreignId('IdCurso')
                  ->constrained('cursos', 'IdCurso');

            $table->foreignId('IdMateria')
                  ->constrained('materias', 'IdMateria');

            // Apunta a la tabla usuarios usando la columna IdUsuario
            $table->foreignId('IdDocente')
                  ->constrained('usuarios', 'IdUsuario');

            $table->foreignId('IdTurno')
                  ->constrained('turnos', 'IdTurno');

            // Fechas operativas y campos adicionales
            $table->dateTime('FechaInicio'); // DATETIME NOT NULL
            $table->dateTime('FechaFin'); // DATETIME NOT NULL
            $table->integer('MaxInscritos')->default(40); // INT NOT NULL DEFAULT 40
            
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // BOOL DEFAULT TRUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos_materias');
    }
};
