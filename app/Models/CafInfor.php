<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafInfor extends Model
{
    use HasFactory;

    protected $table = 'caf_infor'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_caf';

    protected $fillable = [
        'numero_pagina',
        'informacion',
    ];

    public function cafe()
    {
        return $this->belongsTo(Cafe::class, 'id_cafe', 'id_cafe');
    }
}