<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carreraMateriaPensum', function (Blueprint $table) {
            $table->id('IdCarreraMateriaPensum'); // INT AUTO_INCREMENT PRIMARY KEY
            
            // Llaves foráneas
            $table->foreignId('IdCarrera')
                  ->constrained('carreras', 'IdCarrera');

            $table->foreignId('IdMateria')
                  ->constrained('materias', 'IdMateria');

            $table->foreignId('IdPensum')
                  ->constrained('pensum', 'IdPensum');

            $table->integer('Semestre'); // INT NOT NULL
            
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // bit DEFAULT TRUE

            // Restricción única compuesta (Constraint UC_CarreraMateriaPensum)
            $table->unique(['IdCarrera', 'IdMateria', 'IdPensum'], 'UC_CarreraMateriaPensum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carreraMateriaPensum');
    }
};
