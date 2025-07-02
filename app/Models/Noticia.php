<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla. Laravel lo inferiría como 'noticias', así que es opcional,
    // pero lo dejo para mayor claridad.
    protected $table = 'noticias';

    // Define la clave primaria de la tabla 'noticias'.
    protected $primaryKey = 'id_noticias';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'clase',
        'imagen',
        'informacion',
        'numero_pagina',
        'estado',
        'autor',
        'leida',
    ];

    /**
     * Define la relación belongsTo con el modelo User.
     * Una Noticia pertenece a un Usuario (quien la creó).
     */
    public function user()
    {
        // 'user_id' es la clave foránea en la tabla 'noticias'
        // 'id_usuario' es la clave primaria en la tabla 'usuario' (modelo User)
        // Asegúrate de que 'id_usuario' sea la clave primaria de tu tabla de usuarios.
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}