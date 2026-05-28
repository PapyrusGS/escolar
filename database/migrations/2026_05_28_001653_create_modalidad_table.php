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
        Schema::create('modalidad', function (Blueprint $table) {
            $table->id('IdModalidad'); // INT AUTO_INCREMENT PRIMARY KEY (BigInt por defecto en Laravel)
            $table->string('Nombre', 50); // VARCHAR(50) NOT NULL
            $table->integer('DuracionSemanas'); // INT NOT NULL
            $table->integer('MaxMaterias'); // INT NOT NULL
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // BOOL DEFAULT TRUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modalidad');
    }
};
