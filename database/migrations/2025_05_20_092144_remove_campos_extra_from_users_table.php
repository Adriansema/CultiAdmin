<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'apellido',
                'tipo_documento',
                'documento',
                'telefono',
                'token',
                'intentos_fallidos',
                'bloqueado_hasta',
                'codigo_verificacion',
                'id_finca'
            ]);
        });
    }


    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('documento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('token')->nullable();
            $table->integer('intentos_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable();
            $table->string('codigo_verificacion')->nullable();
            $table->unsignedBigInteger('id_finca')->nullable();
        });
    }

};
