<?php

//actualizacion 09/04/2025

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Actions\Fortify\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        // !Aqui es donde definimos el pipeline de autenticacion!
        Fortify::authenticateThrough(function (Request $request) {
            return array_filter([ // Usamos array_filter para eliminar nulos si alguna accion es opcional
                \App\Actions\Fortify\AttemptToAuthenticate::class, // <-- Tu accion personalizada
                // Las acciones predeterminadas de Fortify siguen aqui y son CRUCIALES para la autenticacion de contrasena
                \Laravel\Fortify\Actions\EnsureLoginIsNotThrottled::class, // Para el rate limiting
                \Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable::class, // Si usas 2FA
                \Laravel\Fortify\Actions\AttemptToAuthenticate::class, // <-- La accion predeterminada de Fortify que VERIFICA LA CONTRASEnA
                \Laravel\Fortify\Actions\PrepareAuthenticatedSession::class, // Prepara la sesion si el login es exitoso
            ]);
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
