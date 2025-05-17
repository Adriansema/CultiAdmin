<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'imagen',
        'estado',
        'observaciones',
        'user_id',
        'tipo',
        'detalles_json',
    ];

    protected $casts = [
        'detalles_json' => 'array', // transforma JSON <=> array automáticamente
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

