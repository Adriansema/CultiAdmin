<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_boletins_table.php

   public function up()
{
    Schema::table('boletines', function (Blueprint $table) {
        $table->string('archivo')->nullable()->after('contenido');
    });
}

public function down()
{
    Schema::table('boletines', function (Blueprint $table) {
        $table->dropColumn('archivo');
    });
}

};
