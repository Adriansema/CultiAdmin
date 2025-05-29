<?php

// app/Models/Pqrs.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pqrs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Permitimos user_id porque podría venir de un usuario logueado
        'email',
        'nombre',
        'telefono',
        'asunto',
        'mensaje',
        'tipo',
        'estado',
    ];

    /**
     * Get the user that owns the PQRS.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}