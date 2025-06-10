<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Cambiado de Authenticatable a Model
use Illuminate\Notifications\Notifiable; // Se mantiene si es necesario para notificaciones, de lo contrario se puede eliminar

class Usuario extends Model // Cambiado de Authenticatable a Model
{
    use HasFactory, Notifiable; // Se mantiene Notifiable si es necesario

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_documento',
        'documento',
        'telefono',
        'correo',
        'contrasena',
        'token',
        'intentos_fallidos',
        'bloqueado_hasta',
        'codigo_verificacion',
        'rol',
    ];

    protected $hidden = [
        'contrasena',
        'token',
        'codigo_verificacion',
    ];

    // Se ha eliminado el método getAuthPassword() ya que este modelo no se usará para autenticación.
}
