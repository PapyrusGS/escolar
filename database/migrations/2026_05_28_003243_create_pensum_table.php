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
            
            // Llave foránea hacia la tabla Modalidad (Obligatoria)
            $table->foreignId('IdModalidad')
                  ->constrained('modalidad', 'IdModalidad');

            $table->string('Nombre', 100); // VARCHAR(100) NOT NULL
            $table->integer('NumMaterias'); // INT NOT NULL
            $table->integer('NumSemestres'); // INT NOT NULL
            
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // bit DEFAULT TRUE
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
