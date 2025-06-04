<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafPatoge extends Model
{
    use HasFactory;

    protected $table = 'caf_patoge'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_patoge';

    protected $fillable = [
        'numero_pagina',
        'patogeno',
        'informacion',
    ];

    public function cafe()
    {
        return $this->belongsTo(Cafe::class, 'id_patoge', 'id_patoge');
    }
}
