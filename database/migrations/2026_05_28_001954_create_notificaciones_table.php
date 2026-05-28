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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('IdNotificacion'); // INT AUTO_INCREMENT PRIMARY KEY
            
            // Llave foránea hacia la tabla Usuarios
            $table->foreignId('IdUsuario')
                  ->constrained('usuarios', 'IdUsuario');

            $table->string('Titulo', 100); // VARCHAR(100) NOT NULL
            $table->text('Contenido'); // TEXT NOT NULL
            
            // Campos de fecha con valor por defecto actual
            $table->timestamp('FechaEnvio')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            
            $table->boolean('Estado')->default(true); // BOOL DEFAULT TRUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
