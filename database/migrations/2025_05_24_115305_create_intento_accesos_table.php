<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_intento_accesos_table.php
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
        Schema::create('intento_accesos', function (Blueprint $table) {
            $table->id(); // id (Bigserial)

            // user_id (Int8) - Clave foránea, puede ser nulo según tu especificación
            $table->foreignId('user_id')
                  ->nullable() // Puede ser nulo
                  ->constrained() // Asume 'users' tabla y 'id' columna
                  ->onDelete('set null'); // Si un usuario es eliminado, user_id se establece a NULL

            $table->string('email', 255)->nullable(); // Email (varchar(255), puede ser nulo)
            $table->string('ip_address', 100)->nullable(); // ip_address (varchar(100), puede ser nulo)
            $table->text('user_agent')->nullable(); // user_agent (text, puede ser nulo)

            $table->timestamps(); // created_at y updated_at (timestamp(0))
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intento_accesos');
    }
};