<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperatorUserIdToBoletinesTable extends Migration
{
    public function up()
    {
        Schema::table('boletins', function (Blueprint $table) {
            $table->foreignId('validado_por_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('archivo'); // <-- Añade esta línea

            $table->foreignId('rechazado_por_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('validado_por_user_id'); // <-- Añade esta línea
        });
    }

    public function down()
    {
        Schema::table('boletins', function (Blueprint $table) {
            $table->dropForeign(['validado_por_user_id']);
            $table->dropColumn('validado_por_user_id');
            $table->dropForeign(['rechazado_por_user_id']);
            $table->dropColumn('rechazado_por_user_id');
        });
    }
}