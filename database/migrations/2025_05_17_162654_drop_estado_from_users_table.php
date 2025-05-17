<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropEstadoFromUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Si quieres revertir la migración, recreas la columna
            $table->string('estado')->nullable();
        });
    }
}
