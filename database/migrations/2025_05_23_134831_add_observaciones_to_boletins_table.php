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
        Schema::table('boletins', function (Blueprint $table) {
            // Añade la columna 'observaciones' después de 'estado' (o donde prefieras)
            // Es de tipo 'text' porque puede contener observaciones largas.
            // 'nullable()' permite que no siempre tenga un valor.
            $table->text('observaciones')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boletins', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }
};