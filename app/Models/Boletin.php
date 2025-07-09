<?php

// app/Models/Boletin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Accessors para el formato de precios
    /**
     * Obtiene el precio_mas_alto formateado como moneda ($X.XXX,XX).
     *
     * @return string
     */
    public function getPrecioMasAltoFormattedAttribute(): string
    {
        if (is_null($this->precio_mas_alto)) {
            return 'N/A'; // O un valor predeterminado si no hay precio
        }
        // Formato para espanol de Colombia: 2 decimales, coma para decimales, punto para miles
        return '$' . number_format($this->precio_mas_alto, 2, ',', '.');
    }

    /**
     * Obtiene el precio_mas_bajo formateado como moneda ($X.XXX,XX).
     *
     * @return string
     */
    public function getPrecioMasBajoFormattedAttribute(): string
    {
        if (is_null($this->precio_mas_bajo)) {
            return 'N/A'; // O un valor predeterminado si no hay precio
        }
        // Formato para espanol de Colombia: 2 decimales, coma para decimales, punto para miles
        return '$' . number_format($this->precio_mas_bajo, 2, ',', '.');
    }
}