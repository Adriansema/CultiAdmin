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
            $table->id();
            $table->foreignId('user_id')
                  ->constrained() 
                  ->onDelete('cascade'); // Si un usuario es eliminado, también se borran sus productos

            $table->string('tipo', 255)->default(''); // Tipo (varchar(255), Por defecto '')
            $table->string('imagen', 255)->nullable(); // Imagen (varchar(255), no nulo)
            $table->string('RutaVideo', 255)->nullable();
            $table->text('observaciones')->nullable(); // Observaciones (text, puede ser nulo)
            $table->string('estado', 255)->default('pendiente'); // Estado (varchar(255), Por defecto 'pendiente')

            $table->foreignId('validado_por_user_id')
                  ->nullable() // Puede ser nulo si aún no ha sido validado
                  ->constrained('users') // Referencia a la tabla 'users'
                  ->onDelete('set null'); 
            
            $table->foreignId('rechazado_por_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('validado_por_user_id'); 
            $table->timestamps();
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
