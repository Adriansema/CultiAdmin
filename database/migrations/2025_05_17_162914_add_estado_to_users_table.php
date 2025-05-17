<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Añadir columna 'estado' justo después de 'email' con valor por defecto
        Schema::table('users', function (Blueprint $table) {
            $table->string('estado', 20)->default('activo')->after('email');
        });

        // Agregar restricción CHECK para que solo permita 'activo' o 'inactivo'
        DB::statement("ALTER TABLE users ADD CONSTRAINT estado_check CHECK (estado IN ('activo', 'inactivo'))");
    }

    public function down(): void
    {
        // Primero quitamos la restricción CHECK si existe
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS estado_check");

        // Luego eliminamos la columna
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
