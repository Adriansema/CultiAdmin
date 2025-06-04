<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mora extends Model
{
    use HasFactory;

    protected $table = 'mora';
    protected $primaryKey = 'id_mora';

    protected $fillable = [
        'id_info',
        'id_insu',
        'id_pat',
        'producto_id', // ¡Añadido!
    ];

    // Relaciones
    public function producto() // Nueva relación con Producto
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }

    public function moraInf()
    {
        return $this->belongsTo(MoraInf::class, 'id_info', 'id_info');
    }

    public function moraInsu()
    {
        return $this->belongsTo(MoraInsu::class, 'id_insu', 'id_insu');
    }

    public function moraPatoge()
    {
        return $this->belongsTo(MoraPatoge::class, 'id_pat', 'id_pat');
    }
}
