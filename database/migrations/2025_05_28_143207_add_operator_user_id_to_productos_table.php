<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperatorUserIdToProductosTable extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            // Columna para el ID del usuario que valida
            $table->foreignId('validado_por_user_id')
                  ->nullable() // Puede ser nulo si aún no ha sido validado
                  ->constrained('users') // Referencia a la tabla 'users'
                  ->onDelete('set null') // Si el usuario es eliminado, establece a nulo
                  ->after('estado'); // <-- Añade esta línea para colocarla después de 'estado'

            // Columna para el ID del usuario que rechaza
            $table->foreignId('rechazado_por_user_id')
                  ->nullable() // Puede ser nulo si aún no ha sido rechazado
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('validado_por_user_id'); // <-- Añade esta línea para colocarla después de 'validado_por_user_id'

            // Opcional: Para auditoría de quién fue la última acción (si solo quieres 1 columna)
            // $table->foreignId('ultima_accion_operador_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['validado_por_user_id']);
            $table->dropColumn('validado_por_user_id');
            $table->dropForeign(['rechazado_por_user_id']);
            $table->dropColumn('rechazado_por_user_id');
            // $table->dropForeign(['ultima_accion_operador_id']);
            // $table->dropColumn('ultima_accion_operador_id');
        });
    }
}