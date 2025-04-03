<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = ['contenido'];

    // Función para agrupar por mes
    public static function filtrarPorMes($mes)
    {
        return self::whereMonth('created_at', $mes)->get();
    }

    // Función para eliminar comentarios de más de 2 minutos
    public static function limpiar()
    {
        self::where('created_at', '<', now()->subMinutes(2))->delete();
    }
}
