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
            // Asegúrate de que la tabla 'productos' exista antes de ejecutar esta migración.
            // Si tu tabla principal se llama 'productos', el 'constrained()' por defecto funcionará.
            // Si tu tabla principal tiene otro nombre (ej. 'mis_productos'), usa constrained('mis_productos').

            Schema::table('cafe', function (Blueprint $table) {
                $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('cascade');
            });

            Schema::table('mora', function (Blueprint $table) {
                $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('cafe', function (Blueprint $table) {
                $table->dropForeign(['producto_id']);
                $table->dropColumn('producto_id');
            });

            Schema::table('mora', function (Blueprint $table) {
                $table->dropForeign(['producto_id']);
                $table->dropColumn('producto_id');
            });
        }
    };