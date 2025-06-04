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
            //Agregue la nueva columna 'nombre'
            $table->string('nombre')->after('estado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boletins', function (Blueprint $table) {
            //Esto es para revertir la migración si la ejecutas con rollback
        });
    }
};
