<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoraInf extends Model
{
    use HasFactory;

    protected $table = 'mora_inf'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_info';

    protected $fillable = [
        'numero_pagina',
        'informacion',
    ];

    public function mora()
    {
       return $this->belongsTo(Mora::class, 'id_info', 'id_info');  // Ajusta la FK si es necesario
    }
}