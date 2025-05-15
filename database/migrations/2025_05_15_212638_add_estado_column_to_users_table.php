<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Añadimos la columna con default
        Schema::table('users', function (Blueprint $table) {
            $table->string('estado', 20)->default('activo')->after('email'); // O colócala donde prefieras
        });

        // Agregamos la restricción CHECK para asegurar valores válidos
        DB::statement("ALTER TABLE users ADD CONSTRAINT estado_check CHECK (estado IN ('activo', 'inactivo'))");
    }

    public function down(): void
    {
        // Quitamos la restricción primero
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS estado_check");

        // Eliminamos la columna
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
