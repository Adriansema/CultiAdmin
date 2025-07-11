<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla. Laravel lo inferiria como 'noticias', asi que es opcional,
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
        'observaciones',
        'validado_por_user_id',
        'rechazado_por_user_id',
    ];

    /**
     * Define la relacion belongsTo con el modelo User.
     * Una Noticia pertenece a un Usuario (quien la creo).
     */
    public function user()
    {
        // 'user_id' es la clave foranea en la tabla 'noticias'
        // 'id_usuario' es la clave primaria en la tabla 'usuario' (modelo User)
        // Asegurate de que 'id_usuario' sea la clave primaria de tu tabla de usuarios.
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
