<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_pqrs_table.php

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
        Schema::create('pqrs', function (Blueprint $table) {
            $table->id(); // ID único para cada PQR

            // Si el PQR puede ser enviado por un usuario autenticado, puedes enlazarlo
            // Usamos nullable() porque el PQR del modal vendrá de un usuario NO autenticado
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('email', 255); // Email de contacto del remitente (obligatorio)
            $table->string('nombre', 255)->nullable(); // Nombre del remitente (opcional)
            $table->string('telefono', 50)->nullable(); // Teléfono del remitente (opcional)

            $table->string('asunto', 255); // Asunto o título del PQR
            $table->text('mensaje'); // Contenido detallado del PQR

            // Tipo de PQR (Pregunta, Queja, Reclamo, Sugerencia, etc.)
            $table->enum('tipo', ['pregunta', 'queja', 'reclamo', 'sugerencia'])->default('queja');

            // Estado del PQR (pendiente, en progreso, resuelto, cerrado, etc.)
            $table->enum('estado', ['pendiente', 'en_progreso', 'resuelto', 'cerrado'])->default('pendiente');

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pqrs');
    }
};