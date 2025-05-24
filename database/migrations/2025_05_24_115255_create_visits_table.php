<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_visits_table.php
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
        Schema::create('visits', function (Blueprint $table) {
            $table->id(); // id (Bigserial)

            // user_id (Int8) - Clave foránea, puede ser nulo según tu especificación
            $table->foreignId('user_id')
                  ->nullable() // Puede ser nulo
                  ->constrained() // Asume 'users' tabla y 'id' columna
                  ->onDelete('set null'); // Si un usuario es eliminado, user_id se establece a NULL

            $table->string('page', 255)->nullable(); // Page (varchar(255), puede ser nulo)
            $table->ipAddress('ip')->nullable(); // Ip (inet) - Laravel tiene un helper para IPs
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP')); // created_at (Timestamp(0), no nulo, Por defecto CURRENT_TIMESTAMP)
            // updated_at no está en tu lista, si no lo necesitas, no incluyas $table->timestamps()
            // Si solo quieres created_at, debes definirlo explícitamente y no usar timestamps()
            // Si lo quieres ambos y 'created_at' tiene un default, puedes usar:
            // $table->timestamp('created_at')->useCurrent(); // Laravel 9+ más conciso
            // $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};