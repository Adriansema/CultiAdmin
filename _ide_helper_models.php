<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $estado
 * @property string $contenido
 * @property string|null $archivo
 * @property string|null $observaciones
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $validado_por_user_id
 * @property int|null $rechazado_por_user_id
 * @property string|null $nombre
 * @property-read \App\Models\User|null $rechazador
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\User|null $validador
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereArchivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereContenido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereRechazadoPorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Boletin whereValidadoPorUserId($value)
 */
	class Boletin extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $email
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntentoAcceso whereUserId($value)
 */
	class IntentoAcceso extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $email
 * @property string|null $nombre
 * @property string|null $telefono
 * @property string $asunto
 * @property string $mensaje
 * @property string $tipo
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereAsunto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereMensaje($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pqrs whereUserId($value)
 */
	class Pqrs extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $tipo
 * @property string $estado
 * @property string|null $observaciones
 * @property string $imagen
 * @property array<array-key, mixed>|null $detalles_json
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $validado_por_user_id
 * @property int|null $rechazado_por_user_id
 * @property bool $validado
 * @property-read \App\Models\User|null $rechazador
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User|null $validador
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereDetallesJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereImagen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereRechazadoPorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereValidado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereValidadoPorUserId($value)
 */
	class Producto extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statistic query()
 */
	class Statistic extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $last_login_at
 * @property bool $is_online
 * @property string $estado
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $page
 * @property string|null $ip
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit wherePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereUserId($value)
 */
	class Visit extends \Eloquent {}
}

