<?php

namespace App\Actions\Fortify;

use App\Models\User; // Asegurate de importar tu modelo User
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Models\IntentoAcceso; // Si usas IntentoAcceso
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Validator;    // Importa el facade de Validator
use App\Actions\Fortify\PasswordValidationRules; // Importa el trait que contiene tus reglas de validacion de contrasena
use Illuminate\Validation\ValidationException; // Importa la clase para lanzar excepciones de validacion

class AttemptToAuthenticate
{
    // Usa el trait para tener acceso a passwordRules()
    use PasswordValidationRules;

    public function handle(Request $request, callable $next)
    {
        // --- Logica de validacion de robustez de contrasena ---
        $validator = Validator::make($request->all(), [
            'password' => $this->passwordRules(true), // <-- Pasa 'true' aqui
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'password' => $validator->errors()->first('password'),
            ]);
        }
        // --- FIN de la logica de validacion de robustez de contrasena ---

        $user = User::where(Fortify::username(), $request->{Fortify::username()})->first();

        // Registro de intento de acceso (ajustado para ser mas robusto si el usuario no existe)
        if ($user) {
            IntentoAcceso::create([
                'user_id' => $user->id,
                'email' => $request->{Fortify::username()},
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        } else {
            // Registrar intento incluso si el email no corresponde a un usuario existente
            IntentoAcceso::create([
                'user_id' => null,
                'email' => $request->{Fortify::username()},
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }


        if ($user && $user->estado === 'inactivo') {
            // Disparar un evento de fallo de autenticacion
            event(new Failed('web', $user, $request->merge([
                'password' => '****',
            ])->all()));

            // Redirigir al login con el mensaje de inactivo
            return redirect()->route('login')->withInput($request->only(Fortify::username()))->with('inactivo', true);
        }

        // Si el usuario esta activo o no se encontro, Fortify continua con el siguiente paso del pipeline.
        // Aqui es donde el pipeline de Fortify continuara con la verificacion de contrasena
        // a traves de la accion Laravel\Fortify\Actions\AttemptToAuthenticate::class
        return $next($request);
    }
}
