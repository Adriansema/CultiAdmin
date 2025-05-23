<?php

// app/Models/Boletin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boletin extends Model
{
    use HasFactory;

    protected $table = 'boletins';

    protected $fillable = [
        'archivo',
        'contenido',
        'user_id',       // ¡Asegúrate de que user_id esté aquí!
        'estado',        // ¡Asegúrate de que estado esté aquí!
        'observaciones', // ¡Asegúrate de que observaciones esté aquí!
    ];
}

