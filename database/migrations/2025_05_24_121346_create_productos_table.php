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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('tipo'); // Tipo de producto (cafe, mora, videos)

            // Campos que pueden estar vacios
            $table->string('imagen')->nullable();
            $table->string('rutavideo')->nullable();
            $table->text('observaciones')->nullable();
            
            $table->string('estado')->default('pendiente');

            $table->foreignId('validado_por_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rechazado_por_user_id')->nullable()->constrained('users')->onDelete('set null');

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

