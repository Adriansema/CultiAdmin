<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntentoAcceso extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        // 'created_at' y 'updated_at' son manejados automáticamente por timestamps()
    ];

    /**
     * Get the user that owns the access attempt.
     *
     * Define la relación con el modelo User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}