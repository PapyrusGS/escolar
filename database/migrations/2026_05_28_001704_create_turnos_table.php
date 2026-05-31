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
        Schema::create('turnos', function (Blueprint $table) {
            $table->id('IdTurno'); // INT AUTO_INCREMENT PRIMARY KEY
            $table->string('Nombre', 50); // VARCHAR(50) NOT NULL
            $table->time('HoraInicio'); // TIME NOT NULL
            $table->time('HoraFin'); // TIME NOT NULL
            
            // Días de la semana (Campos bit NOT NULL mapeados como booleanos)
            $table->boolean('Lun');
            $table->boolean('Mar');
            $table->boolean('Mie');
            $table->boolean('Jue');
            $table->boolean('Vie');
            $table->boolean('Sab');
            $table->boolean('Dom');
            
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // bit DEFAULT TRUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
