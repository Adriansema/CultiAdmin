<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoraPatoge extends Model
{
    use HasFactory;

    protected $table = 'mora_patoge'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_pat'; 

    protected $fillable = [
        'numero_pagina',
        'patogeno',
        'informacion',
    ];

    public function mora()
    {
       return $this->belongsTo(Mora::class, 'id_pat', 'id_pat');// Ajusta la FK si es necesario
    }
}