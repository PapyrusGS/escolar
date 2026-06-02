<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias_aprobadas', function (Blueprint $table) {
            $table->id('IdMateriaAprobada');
            $table->foreignId('IdUsuario')->constrained('usuarios', 'IdUsuario');
            $table->foreignId('IdMateria')->constrained('materias', 'IdMateria');
            $table->foreignId('IdCarrera')->constrained('carreras', 'IdCarrera');
            $table->decimal('NotaFinal', 5, 2)->nullable();
            $table->date('FechaAprobacion');
            $table->boolean('Estado')->default(true);
            $table->unique(['IdUsuario', 'IdMateria']);
        });

        DB::statement(<<<SQL
            INSERT INTO materias_aprobadas (IdUsuario, IdMateria, IdCarrera, NotaFinal, FechaAprobacion, Estado)
            SELECT ec.IdUsuario, cm.IdMateria, ec.IdCarrera, MAX(n.Nota), CURRENT_DATE, 1
            FROM notas n
            JOIN inscripciones i ON n.IdInscripcion = i.IdInscripcion
            JOIN cursos_materias cm ON i.IdCursoMateria = cm.IdCursoMateria
            JOIN EstudianteCarrera ec ON i.IdEstudiante = ec.IdEstudianteCarrera
            WHERE n.Estado = 1
              AND n.Nota >= 51
              AND i.Estado = 1
              AND cm.Estado = 1
            GROUP BY ec.IdUsuario, cm.IdMateria, ec.IdCarrera
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('materias_aprobadas');
    }
};
