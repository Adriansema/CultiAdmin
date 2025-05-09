<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'imagen', 'estado', 'observaciones', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
