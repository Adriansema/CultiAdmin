<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade la columna 'estado' a la tabla 'users'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Añade la columna 'estado' de tipo string con longitud 20.
            // Establece un valor por defecto de 'activo'.
            // La columna es NOT NULL por defecto en Laravel si no se usa ->nullable().
            $table->string('estado', 20)->default('activo')->after('email'); // Puedes ajustar '.after('email')' si quieres que aparezca después de otra columna específica
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la columna 'estado' de la tabla 'users'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Elimina la columna 'estado' si se revierte la migración.
            $table->dropColumn('estado');
        });
    }
};
