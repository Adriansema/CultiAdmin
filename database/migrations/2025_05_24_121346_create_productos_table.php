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
        Schema::create('productos', function (Blueprint $table) {
            $table->id(); // id (Bigserial)

            // user_id (int8) - Clave foránea, no nulo según tu especificación
            $table->foreignId('user_id')
                  ->constrained() // Asume 'users' tabla y 'id' columna, no nulo
                  ->onDelete('cascade'); // Si un usuario es eliminado, también se borran sus productos

            $table->string('tipo', 255)->default(''); // Tipo (varchar(255), Por defecto '')
            $table->string('estado', 255)->default('pendiente'); // Estado (varchar(255), Por defecto 'pendiente')
            $table->text('observaciones')->nullable(); // Observaciones (text, puede ser nulo)
            // ¡COLUMNA 'descripcion' ELIMINADA!
            $table->string('imagen', 255); // Imagen (varchar(255), no nulo)
            $table->json('detalles_json')->nullable(); // detalles_json (json, puede ser nulo)

            $table->timestamps(); // created_at y updated_at (timestamp(0))
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
