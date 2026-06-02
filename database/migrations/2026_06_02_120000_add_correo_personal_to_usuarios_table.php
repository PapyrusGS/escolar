<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('CorreoPersonal', 100)->nullable()->unique()->after('Correo');
        });

        DB::statement('UPDATE usuarios SET CorreoPersonal = Correo WHERE CorreoPersonal IS NULL');
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropUnique(['CorreoPersonal']);
            $table->dropColumn('CorreoPersonal');
        });
    }
};
