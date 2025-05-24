<?php
// database/migrations/YYYY_MM_DD_HHMMSS_add_estado_to_users_table.php
// (Asegúrate de que YYYY_MM_DD_HHMMSS sea posterior a la de 'create_users_table')

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
        Schema::table('users', function (Blueprint $table) {
            // estado (varchar(20), Por defecto 'activo')
            $table->string('estado', 20)->default('activo')->after('password'); // Añadir después de 'password' o donde prefieras
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};