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
            $table->decimal('precio_mas_alto', 10, 2)->nullable()->after('ruta_pdf');
            $table->string('lugar_precio_mas_alto', 255)->nullable()->after('precio_mas_alto');
            $table->decimal('precio_mas_bajo', 10, 2)->nullable()->after('lugar_precio_mas_alto');
            $table->string('lugar_precio_mas_bajo', 255)->nullable()->after('precio_mas_bajo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boletines', function (Blueprint $table) {
            $table->dropColumn([
                'precio_mas_alto',
                'lugar_precio_mas_alto',
                'precio_mas_bajo',
                'lugar_precio_mas_bajo',
            ]);
        });
    }
};
