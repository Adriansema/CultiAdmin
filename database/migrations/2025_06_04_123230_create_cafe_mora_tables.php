<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ejecuta las migraciones para crear las tablas con claves foráneas.
     */
    public function up(): void
    {
        if (!Schema::hasTable('usuario')) {
            Schema::create('usuario', function (Blueprint $table) {
                $table->increments('id_usuario');
                $table->string('nombre', 255)->nullable();
                $table->string('apellido', 255)->nullable();
                $table->string('tipo_documento', 50)->nullable();
                $table->string('documento', 50)->unique()->nullable();
                $table->string('telefono', 20)->nullable();
                $table->string('correo', 255)->unique()->nullable();
                $table->string('contrasena', 255)->nullable();
                $table->string('token', 255)->nullable();
                $table->integer('intentos_fallidos')->default(0);
                $table->timestamp('bloqueado_hasta')->nullable();
                $table->string('codigo_verificacion', 100)->nullable();
                $table->string('rol', 50)->nullable();
                $table->timestamps(); 
            });
        }

        // Tabla cafe
        Schema::create('cafe', function (Blueprint $table) {
            $table->increments('id_cafe');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('numero_pagina'); // int4 y NOT NULL
            $table->string('clase', 100)->nullable()->default(null);
            $table->text('informacion')->nullable()->default(null);
            $table->timestamps();
        });

        // Tabla mora
        Schema::create('mora', function (Blueprint $table) {
            $table->increments('id_mora'); 
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('numero_pagina');
            $table->string('clase', 100)->nullable()->default(null);
            $table->text('informacion')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('finca', function (Blueprint $table) {
            $table->increments('id_finca');
            $table->foreignId('id_usuario')->constrained('usuario', 'id_usuario')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('nombrefinca', 100)->nullable()->default(null);
            $table->string('ubicacion', 100)->nullable()->default(null);
            $table->string('cultivo', 50)->nullable()->default(null);
            $table->date('fundacion')->nullable();
            $table->decimal('hectareas', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Revierte las migraciones eliminando las tablas en orden inverso de dependencia.
     */
    public function down(): void
    {
        Schema::dropIfExists('finca'); 
        Schema::dropIfExists('mora');  
        Schema::dropIfExists('cafe');  
        if (Schema::hasTable('usuario')) { 
            Schema::dropIfExists('usuario');
        }
    }
};
