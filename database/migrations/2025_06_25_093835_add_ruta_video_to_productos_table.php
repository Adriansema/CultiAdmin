<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ejecuta las migraciones para añadir la columna 'RutaVideo' a la tabla 'productos'.
     */
    public function up(): void
    {
        // Verifica si la tabla 'productos' existe y si la columna 'RutaVideo' no existe.
        // Esto previene errores si la columna ya fue añadida o la tabla no existe.
        if (Schema::hasTable('productos') && !Schema::hasColumn('productos', 'RutaVideo')) {
            Schema::table('productos', function (Blueprint $table) {
                // Añade la nueva columna 'RutaVideo' como string, que puede ser nula.
                // La ubicación 'after('imagen')' es opcional, puedes ajustarla si quieres que aparezca en otro lugar.
                $table->string('RutaVideo', 255)->nullable()->after('imagen');
            });
        }
    }

    /**
     * Reverse the migrations.
     * Revierte las migraciones eliminando la columna 'RutaVideo' de la tabla 'productos'.
     */
    public function down(): void
    {
        // Verifica si la tabla 'productos' existe y si la columna 'RutaVideo' existe.
        if (Schema::hasTable('productos') && Schema::hasColumn('productos', 'RutaVideo')) {
            Schema::table('productos', function (Blueprint $table) {
                // Elimina la columna 'RutaVideo'.
                $table->dropColumn('RutaVideo');
            });
        }
    }
};
