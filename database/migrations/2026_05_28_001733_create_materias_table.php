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
            
            // Llave foránea auto-referenciada (Opcional porque no todas tienen prerrequisito)
            $table->foreignId('IdMateriaPrevia')
                  ->nullable()
                  ->constrained('materias', 'IdMateria');

            $table->string('CodigoMateria', 20)->unique(); // VARCHAR(20) NOT NULL UNIQUE
            $table->string('Nombre', 100); // VARCHAR(100) NOT NULL
            $table->string('Descripcion', 255)->nullable(); // VARCHAR(255) (Permite nulos)
            
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // bit DEFAULT TRUE
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
