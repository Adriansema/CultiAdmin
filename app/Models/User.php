<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Permite que este modelo maneje la autenticación (login/logout).
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable // Este modelo es ahora un "usuario autenticable" para Laravel.
{
    use HasApiTokens;              // Para tokens de API (Sanctum).
    use HasRoles;                  // Para roles y permisos (Spatie).
    use HasRelationships;
    use HasFactory;
    use HasProfilePhoto;           // Para fotos de perfil (Jetstream).
    use Notifiable;                // Para notificaciones (ej. emails).
    use TwoFactorAuthenticatable;  // Para autenticación de dos factores (Fortify).

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password', // Se permite asignar el valor, pero siempre será un hash (no texto plano).
        'estado',
    ];

    protected $hidden = [
        'password',       // Oculta el hash de la contraseña en respuestas JSON/arrays por seguridad.
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+: Asegura que la contraseña siempre se guarde hasheada y se verifique automáticamente.
        'estado' => 'string',
    ];

    protected $guard_name = 'web'; // <-- IMPORTANTE para Spatie Permissions: Define qué 'guard' usa este modelo para roles/permisos.
                                  // Conecta roles/permisos asignados a este usuario con el guard 'web'.
}