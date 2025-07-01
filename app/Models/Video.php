<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'producto_id',
        'autor',
        'titulo',
        'descripcion',
        'rutaVideo',
        'tipo', // Este campo guarda el subtipo (primarios, secundarios, categorias)
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');

    }
}
