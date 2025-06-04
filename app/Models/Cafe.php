<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cafe extends Model
{
    use HasFactory;

    protected $table = 'cafe';
    protected $primaryKey = 'id_cafe';

    protected $fillable = [
        'id_caf',
        'id_patoge',
        'id_insumos',
        'producto_id',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }

    public function cafInfor()
    {
        return $this->belongsTo(CafInfor::class, 'id_caf', 'id_caf');
    }

    public function cafPatoge()
    {
        return $this->belongsTo(CafPatoge::class, 'id_patoge', 'id_patoge');
    }

    public function cafInsumos()
    {
        return $this->belongsTo(CafInsumos::class, 'id_insumos', 'id_insumos');
    }
}