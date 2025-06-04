<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ejecuta las migraciones para crear las tablas.
     */
    public function up(): void
    {
        // 1. Crear tablas sin dependencias de claves foráneas (o con dependencias mínimas)
        // Estas tablas no referencian a ninguna otra tabla que estemos creando en esta migración
        // o sus dependencias se resuelven más adelante.

        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario'); // Clave primaria autoincremental
            $table->string('nombre', 255)->nullable();
            $table->string('apellido', 255)->nullable();
            $table->string('tipo_documento', 50)->nullable();
            $table->string('documento', 50)->unique()->nullable(); // Documento único
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 255)->unique()->nullable(); // Correo único
            $table->string('contrasena', 255)->nullable();
            $table->string('token', 255)->nullable();
            $table->integer('intentos_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable(); // Fecha y hora hasta la que el usuario está bloqueado
            $table->string('codigo_verificacion', 100)->nullable();
            $table->string('rol', 50)->nullable();
            $table->timestamps(); // created_at y updated_at
        });

        Schema::create('caf_infor', function (Blueprint $table) {
            $table->id('id_caf'); // Clave primaria autoincremental
            $table->integer('numero_pagina')->nullable();
            $table->text('informacion')->nullable();
            $table->timestamps();
        });

        Schema::create('caf_insumos', function (Blueprint $table) {
            $table->id('id_insumos'); // Clave primaria autoincremental
            $table->integer('numero_pagina')->nullable();
            $table->text('informacion')->nullable();
            $table->timestamps();
        });

        Schema::create('caf_patoge', function (Blueprint $table) {
            $table->id('id_patoge'); // Clave primaria autoincremental
            $table->integer('numero_pagina')->nullable();
            $table->string('patogeno', 255)->nullable();
            $table->text('informacion')->nullable();
            $table->timestamps();
        });

        Schema::create('mora_inf', function (Blueprint $table) {
            $table->id('id_info'); // Clave primaria autoincremental
            $table->integer('numero_pagina')->nullable();
            $table->text('informacion')->nullable();
            $table->timestamps();
        });

        Schema::create('mora_insu', function (Blueprint $table) {
            $table->id('id_insu'); // Clave primaria autoincremental
            $table->integer('numero_pagina')->nullable();
            $table->text('informacion')->nullable();
            $table->timestamps();
        });

        Schema::create('mora_patoge', function (Blueprint $table) {
            $table->id('id_pat'); // Clave primaria autoincremental
            $table->integer('numero_pagina')->nullable();
            $table->string('patogeno', 255)->nullable();
            $table->text('informacion')->nullable();
            $table->timestamps();
        });

        // 2. Crear tablas que dependen de las tablas ya creadas en el paso 1

        Schema::create('cafe', function (Blueprint $table) {
            $table->id('id_cafe'); // Clave primaria autoincremental
            $table->foreignId('id_caf')->nullable()->constrained('caf_infor', 'id_caf')->onDelete('set null');
            $table->foreignId('id_patoge')->nullable()->constrained('caf_patoge', 'id_patoge')->onDelete('set null');
            $table->foreignId('id_insumos')->nullable()->constrained('caf_insumos', 'id_insumos')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('mora', function (Blueprint $table) {
            $table->id('id_mora'); // Clave primaria autoincremental
            $table->foreignId('id_info')->nullable()->constrained('mora_inf', 'id_info')->onDelete('set null');
            $table->foreignId('id_insu')->nullable()->constrained('mora_insu', 'id_insu')->onDelete('set null');
            $table->foreignId('id_pat')->nullable()->constrained('mora_patoge', 'id_pat')->onDelete('set null');
            $table->timestamps();
        });

        // 3. Crear tablas que dependen de las tablas creadas en el paso 2 (o de pasos anteriores)

        Schema::create('cultivos', function (Blueprint $table) {
            $table->id('id_cultivos'); // Clave primaria autoincremental
            $table->foreignId('id_cafe')->nullable()->constrained('cafe', 'id_cafe')->onDelete('set null');
            $table->foreignId('id_mora')->nullable()->constrained('mora', 'id_mora')->onDelete('set null');
            $table->timestamps();
        });

        // 4. Crear tablas finales que dependen de las tablas anteriores

        Schema::create('finca', function (Blueprint $table) {
            $table->id('id_finca'); // Clave primaria autoincremental
            $table->string('nombre_finca', 255)->nullable();
            $table->string('ubicacion', 255)->nullable();
            $table->decimal('hectareas', 10, 2)->nullable();
            $table->date('fundacion')->nullable();
            // Asumo que 'id_cultivo' en finca se refiere a 'id_cultivos' de la tabla 'cultivos'
            $table->foreignId('id_cultivo')->nullable()->constrained('cultivos', 'id_cultivos')->onDelete('set null');
            $table->foreignId('id_usuario')->nullable()->constrained('usuario', 'id_usuario')->onDelete('set null');
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
        Schema::dropIfExists('cultivos');
        Schema::dropIfExists('mora');
        Schema::dropIfExists('cafe');
        Schema::dropIfExists('mora_patoge');
        Schema::dropIfExists('mora_insu');
        Schema::dropIfExists('mora_inf');
        Schema::dropIfExists('caf_patoge');
        Schema::dropIfExists('caf_insumos');
        Schema::dropIfExists('caf_infor');
        Schema::dropIfExists('usuario');
    }
};