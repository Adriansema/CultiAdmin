<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoraInsu extends Model
{
    use HasFactory;

    protected $table = 'mora_insu'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_insu';

    protected $fillable = [
        'numero_pagina',
        'informacion',
    ];

    public function mora()
    {
       return $this->belongsTo(Mora::class, 'id_insu', 'id_insu'); // Ajusta la FK si es necesario
    }
}
