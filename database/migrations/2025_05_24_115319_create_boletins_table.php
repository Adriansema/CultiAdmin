<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_boletins_table.php
// (Asegúrate de que YYYY_MM_DD_HHMMSS sea posterior a la de 'users')

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
        Schema::create('boletins', function (Blueprint $table) {
            $table->id(); // id (Bigserial)

            // user_id (Int8) - Clave foránea, puede ser nulo según tu especificación
            $table->foreignId('user_id')
                  ->nullable() // Puede ser nulo
                  ->constrained() // Asume 'users' tabla y 'id' columna
                  ->onDelete('set null'); // Si un usuario es eliminado, user_id se establece a NULL
            $table->string('nombre')->after('estado')->nullable();
            $table->text('descripcion'); // descripcion (text, no nulo)
            $table->string('archivo', 255)->nullable(); // archivo (varchar(255), puede ser nulo)
            $table->text('observaciones')->nullable(); // Observaciones (text, puede ser nulo)
            $table->string('estado', 255)->default('pendiente'); // Estado (varchar(255), Por defecto 'pendiente')
            $table->foreignId('validado_por_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null'); // <-- Añade esta línea

            $table->foreignId('rechazado_por_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('validado_por_user_id'); // <-- Añade esta línea
            $table->timestamps(); // created_at y updated_at (timestamp(0))
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletins');
    }
};