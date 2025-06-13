<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mora extends Model
{
    use HasFactory;

    protected $table = 'mora';
    protected $primaryKey = 'id_mora';

    protected $fillable = [
        'producto_id', 
        'numero_pagina',
        'clase',
        'informacion',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
