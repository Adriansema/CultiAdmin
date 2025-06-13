<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finca extends Model
{
    use HasFactory;

    protected $table = 'finca';
    protected $primaryKey = 'id_finca';

    protected $fillable = [
        'id_usuario',
        'producto_id',
        'nombrefinca',
        'ubicacion',
        'cultivo',
        'fundacion',
        'hectareas',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
