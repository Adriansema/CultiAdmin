<?php

// app/Models/Boletin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Boletin extends Model
{
    use HasFactory;

    protected $table = 'boletins';

    protected $fillable = [
        'user_id',
        'estado',
        'nombre',
        'descripcion',
        'archivo',
        'validado_por_user_id',
        'rechazado_por_user_id',
        'observaciones',
        'precio_mas_alto',
        'lugar_precio_mas_alto',
        'precio_mas_bajo',
        'lugar_precio_mas_bajo',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por_user_id');
    }

    public function rechazador()
    {
        return $this->belongsTo(User::class, 'rechazado_por_user_id');
    }

    // Define the casts for attributes (optional, but good practice for numerical fields)
    protected $casts = [
        'precio_mas_alto' => 'decimal:2', // Guarda con 2 decimales en la DB
        'precio_mas_bajo' => 'decimal:2', // Guarda con 2 decimales en la DB
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     // --- Accessor para precio_mas_alto_formatted ---
    protected function precioMasAltoFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['precio_mas_alto'] !== null ?
                '$' . number_format($attributes['precio_mas_alto'], 2, ',', '.') . ' COP' :
                null,
        );
    }

    // --- Accessor para precio_mas_bajo_formatted ---
    protected function precioMasBajoFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['precio_mas_bajo'] !== null ?
                '$' . number_format($attributes['precio_mas_bajo'], 2, ',', '.') . ' COP' :
                null,
        );
    }
}