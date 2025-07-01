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
        'user_id',
        'estado',
        'nombre',
        'contenido',
        'archivo',
        'validado_por_user_id', // <-- Nueva columna
        'rechazado_por_user_id', // <-- Nueva columna
        'observaciones',
        'precio_mas_alto',       
        'lugar_precio_mas_alto', 
        'precio_mas_bajo',      
        'lugar_precio_mas_bajo', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por_user_id');
    }

    public function rechazador()
    {
        return $this->belongsTo(User::class, 'rechazado_por_user_id');
    }
}

