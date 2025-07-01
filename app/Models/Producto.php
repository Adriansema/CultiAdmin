<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'user_id',
        'tipo',
        'estado',
        'validado_por_user_id',
        'rechazado_por_user_id',
        'observaciones',
        'imagen',
        'RutaVideo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación para el usuario que validó
    public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por_user_id');
    }

    // Relación para el usuario que rechazó
    public function rechazador()
    {
        return $this->belongsTo(User::class, 'rechazado_por_user_id');
    }

    // Relación para cafe
    public function cafe()
    {
        return $this->hasOne(Cafe::class, 'producto_id', 'id');
    }

    // Relación para mora
    public function mora()
    {
        return $this->hasOne(Mora::class, 'producto_id', 'id');
    }
    
    public function videos()
    {
        return $this->hasOne(Video::class, 'producto_id', 'id');
    }
}
