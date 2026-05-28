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
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id('IdInscripcion'); // INT AUTO_INCREMENT PRIMARY KEY
            
            // Llave foránea hacia Usuarios (mapeado como IdEstudiante)
            $table->foreignId('IdEstudiante')
                  ->constrained('usuarios', 'IdUsuario');

            // Llave foránea hacia CursosMaterias
            $table->foreignId('IdCursoMateria')
                  ->constrained('cursos_materias', 'IdCursoMateria');

            $table->timestamp('Fecha')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // BOOL DEFAULT TRUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
