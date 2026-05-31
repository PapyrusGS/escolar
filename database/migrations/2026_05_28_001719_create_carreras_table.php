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
        Schema::create('carreras', function (Blueprint $table) {
            $table->id('IdCarrera'); // INT AUTO_INCREMENT PRIMARY KEY
            $table->string('Nombre', 100); // VARCHAR(100) NOT NULL
            $table->string('Descripcion', 255)->nullable(); // VARCHAR(255) (Permite nulos)
            $table->timestamp('FechaRegistro')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->boolean('Estado')->default(true); // bit DEFAULT TRUE (Mapeado como boolean)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};
