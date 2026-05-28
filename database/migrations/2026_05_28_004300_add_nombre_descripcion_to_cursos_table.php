<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            if (! Schema::hasColumn('cursos', 'Nombre')) {
                $table->string('Nombre', 100)->nullable()->after('IdCurso');
            }

            if (! Schema::hasColumn('cursos', 'Descripcion')) {
                $table->string('Descripcion', 255)->nullable()->after('Nombre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            if (Schema::hasColumn('cursos', 'Descripcion')) {
                $table->dropColumn('Descripcion');
            }

            if (Schema::hasColumn('cursos', 'Nombre')) {
                $table->dropColumn('Nombre');
            }
        });
    }
};
