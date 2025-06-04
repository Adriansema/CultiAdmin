<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cultivo extends Model
{
    use HasFactory;

    protected $table = 'cultivos';
    protected $primaryKey = 'id_cultivos';

    protected $fillable = [
        'id_cafe',
        'id_mora',
    ];

    // Relaciones
    public function cafe()
    {
        return $this->belongsTo(Cafe::class, 'id_cafe', 'id_cafe');
    }

    public function mora()
    {
        return $this->belongsTo(Mora::class, 'id_mora', 'id_mora');
    }
}