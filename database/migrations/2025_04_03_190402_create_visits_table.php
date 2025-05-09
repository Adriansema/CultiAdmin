<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('page')->nullable(); // Página visitada (opcional)
            $table->ipAddress('ip'); // Dirección IP del visitante
            $table->timestamp('created_at')->useCurrent(); // Fecha de la visita
        });
    }

    public function down()
    {
        Schema::dropIfExists('visits');
    }
};


