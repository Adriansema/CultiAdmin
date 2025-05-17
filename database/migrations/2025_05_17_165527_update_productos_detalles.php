<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Eliminar columnas viejas
            if (Schema::hasColumn('productos', 'nombre')) {
                $table->dropColumn('nombre');
            }

            if (Schema::hasColumn('productos', 'descripcion')) {
                $table->dropColumn('descripcion');
            }

            // Agregar columnas nuevas
            $table->json('detalles_json')->nullable()->after('id'); // ajusta el after como quieras
            $table->string('tipo')->default('')->after('detalles_json');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Revertir: agregar columnas viejas
            $table->string('nombre')->nullable()->after('id');
            $table->text('descripcion')->nullable()->after('nombre');

            // Eliminar columnas nuevas
            $table->dropColumn('detalles_json');
            $table->dropColumn('tipo');
        });
    }
};

