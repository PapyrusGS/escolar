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
        Schema::create('materias', function (Blueprint $table) {
            $table->id('IdMateria'); // INT AUTO_INCREMENT PRIMARY KEY
            
            // Llave foránea hacia la tabla Carreras (Obligatoria)
            $table->foreignId('IdCarrera')
                  ->constrained('carreras', 'IdCarrera');
            
            // Llave foránea auto-referenciada para la materia previa (Opcional/Nullable)
            $table->foreignId('IdMateriaPrevia')
                  ->nullable() // Permite nulos porque no todas las materias tienen prerrequisito
                  ->constrained('materias', 'IdMateria');

            $table->string('Nombre', 100); // VARCHAR(100) NOT NULL
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // BOOL DEFAULT TRUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
