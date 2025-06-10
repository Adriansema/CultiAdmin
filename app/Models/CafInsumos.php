<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafInsumos extends Model
{
    use HasFactory;

    protected $table = 'caf_insumos'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_insumos';

    protected $fillable = [
        'numero_pagina',
        'informacion',
    ];

    public function cafe()
    {
        return $this->belongsTo(Cafe::class, 'id_insumos', 'id_insumos');
    }
}
