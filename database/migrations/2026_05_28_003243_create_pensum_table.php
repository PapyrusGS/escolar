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
        Schema::create('pensum', function (Blueprint $table) {
            $table->id('IdPensum'); // INT AUTO_INCREMENT PRIMARY KEY
            
            // Llave foránea hacia la tabla Carreras
            $table->foreignId('IdCarrera')
                  ->constrained('carreras', 'IdCarrera');

            // Llave foránea hacia la tabla Materias
            $table->foreignId('IdMateria')
                  ->constrained('materias', 'IdMateria');

            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // BOOL DEFAULT TRUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pensum');
    }
};
