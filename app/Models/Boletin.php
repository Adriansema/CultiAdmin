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
        'contenido',
        'archivo',  
        'validado_por_user_id', // <-- Nueva columna
        'rechazado_por_user_id', // <-- Nueva columna   
        'observaciones', 
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

