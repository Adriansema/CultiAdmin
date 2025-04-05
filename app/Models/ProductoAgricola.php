<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoAgricola extends Model
{
    protected $table = 'productos_agricolas';
    protected $fillable = [
        'nombre',
        'tipo',
        'suelo',
        'caracteristicas',
        'estado',
        'observaciones',
        'imagen',
    ];

   
}
