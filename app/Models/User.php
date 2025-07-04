<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\CustomResetPassword; // <-- Esta importación está CORRECTA y es necesaria.

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    use HasRelationships;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    // use CustomResetPassword; // <-- ¡ELIMINA ESTA LÍNEA! Es el problema.

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'estado',
        'type_document',
        'document',
        'lastname',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'estado' => 'string',
    ];

    protected $guard_name = 'web';

    public function isAdmin(): bool
    {
        return $this->hasRole('administrador');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'user_id', 'id');
    }

    public function noticias()
    {
        return $this->hasMany(Noticia::class, 'user_id', 'id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // Esta línea está bien porque la importación de arriba (`use App\Notifications\CustomResetPassword;`)
        // le dice a PHP dónde encontrar la clase `CustomResetPassword` cuando la instanciamos aquí.
        $this->notify(new CustomResetPassword($token));
    }
}