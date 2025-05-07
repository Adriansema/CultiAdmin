<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // caf_infor
        Schema::create('caf_infor', function (Blueprint $table) {
            $table->id('id_caf');
            $table->integer('numero_pagina');
            $table->text('informacion');
        });

        // caf_patoge
        Schema::create('caf_patoge', function (Blueprint $table) {
            $table->id('id_patoge');
            $table->integer('numero_pagina');
            $table->string('patogeno', 100);
            $table->text('informacion');
        });

        // caf_insumos
        Schema::create('caf_insumos', function (Blueprint $table) {
            $table->id('id_insumos');
            $table->text('parrafo');
            $table->text('informacion');
        });

        // cafe
        Schema::create('cafe', function (Blueprint $table) {
            $table->id('id_cafe');
            $table->unsignedBigInteger('id_caf');
            $table->unsignedBigInteger('id_patoge');
            $table->unsignedBigInteger('id_insumos');

            $table->foreign('id_caf')->references('id_caf')->on('caf_infor');
            $table->foreign('id_patoge')->references('id_patoge')->on('caf_patoge');
            $table->foreign('id_insumos')->references('id_insumos')->on('caf_insumos');
        });

        // mora
        Schema::create('mora', function (Blueprint $table) {
            $table->id('id_mora');
            $table->string('especie', 100);
            $table->string('variedad', 100);
            $table->text('propiedades');
            $table->text('caracteristicas');
        });

        // cultivos
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id('id_cultivos');
            $table->unsignedBigInteger('id_cafe')->nullable();
            $table->unsignedBigInteger('id_mora')->nullable();
            $table->string('nombre_cultivo', 100);
            $table->string('tipo_cultivo', 100);

            $table->foreign('id_cafe')->references('id_cafe')->on('cafe');
            $table->foreign('id_mora')->references('id_mora')->on('mora');
        });

        // registros
        Schema::create('registros', function (Blueprint $table) {
            $table->id('id_registros');
            $table->string('seleccione_cultivo', 100);
            $table->string('etapa_cultivo', 100);
            $table->text('actividad_realizada');
            $table->text('descripcion');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_final');
        });

        // finca
        Schema::create('finca', function (Blueprint $table) {
            $table->id('id_finca');
            $table->unsignedBigInteger('id_registros')->nullable();
            $table->unsignedBigInteger('id_cultivos')->nullable();
            $table->string('nombre', 100);
            $table->text('direccion');
            $table->decimal('hectareas');

            $table->foreign('id_registros')->references('id_registros')->on('registros');
            $table->foreign('id_cultivos')->references('id_cultivos')->on('cultivos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('finca');
        Schema::dropIfExists('registros');
        Schema::dropIfExists('cultivos');
        Schema::dropIfExists('cafe');
        Schema::dropIfExists('mora');
        Schema::dropIfExists('caf_infor');
        Schema::dropIfExists('caf_patoge');
        Schema::dropIfExists('caf_insumos');
    }
};
 //Creamos todas las tablas necesarias para el funcionamiento de la aplicacion
// y sus relaciones
// Se crean las tablas: caf_infor, caf_patoge, caf_insumos, cafe, mora, cultivos, registros y finca
// Se definen las columnas y sus tipos de datos
