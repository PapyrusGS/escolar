<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('EstudianteCarrera', function (Blueprint $table) {
            $table->id('IdEstudianteCarrera');
            $table->foreignId('IdUsuario')
                ->constrained('usuarios', 'IdUsuario');
            $table->foreignId('IdCarrera')
                ->constrained('carreras', 'IdCarrera');
            $table->timestamp('FechaRegistro')->useCurrent();
            $table->boolean('Estado')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('EstudianteCarrera');
    }
};
