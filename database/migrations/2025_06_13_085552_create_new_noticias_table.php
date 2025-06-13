<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ejecuta las migraciones para crear la tabla 'noticias'.
     */
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->increments('id_noticias');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo', 255)->default(''); // Tipo (ej. 'cafe', 'mora')
            $table->string('titulo', 100)->nullable()->default(null);
            $table->string('clase', 100)->nullable()->default(null);
            $table->string('imagen', 255)->nullable(); // Ruta de la imagen
            $table->text('informacion')->nullable()->default(null);
            $table->integer('numero_pagina'); // int4 y NOT NULL
            $table->string('estado', 255)->default('pendiente'); // Estado (ej. 'pendiente', 'aprobada', 'rechazada')
            $table->string('autor', 255)->nullable()->default(null);
            $table->timestamps(); // Columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     * Revierte las migraciones eliminando la tabla 'noticias'.
     */
    public function down(): void
    {
    // Elimina la tabla 'noticias' si existe
        Schema::dropIfExists('noticias');
    }
};