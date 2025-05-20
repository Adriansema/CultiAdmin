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
             $table->string('archivo')->nullable(); // ← campo para la ruta del PDF
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boletins', function (Blueprint $table) {
            $table->dropColumn('archivo');
            //
        });
    }
};
