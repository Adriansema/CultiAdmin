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
        'nombre_finca',
        'ubicacion',
        'hectareas',
        'fundacion',
        'id_cultivo',
        'id_usuario',
    ];

    // Relaciones
    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class, 'id_cultivo', 'id_cultivos');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
