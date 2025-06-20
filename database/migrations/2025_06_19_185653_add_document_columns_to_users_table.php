<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type_document', 50)->after('email')->nullable(); // Despues de 'email' por ejemplo
            $table->string('document')->unique()->after('type_document')->nullable(); // El numero de documento, debe ser unico
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['type_document', 'document']);
        });
    }
};
