<?php

namespace App\Actions\Fortify;

use App\Models\User; // Asegúrate de importar tu modelo User
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Models\IntentoAcceso; // Si usas IntentoAcceso
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Validator;    // Importa el facade de Validator
use App\Actions\Fortify\PasswordValidationRules; // Importa el trait que contiene tus reglas de validación de contraseña
use Illuminate\Validation\ValidationException; // Importa la clase para lanzar excepciones de validación

class AttemptToAuthenticate
{
    // Usa el trait para tener acceso a passwordRules()
    use PasswordValidationRules;

    public function handle(Request $request, callable $next)
    {
        // --- Lógica de validación de robustez de contraseña ---
        $validator = Validator::make($request->all(), [
            'password' => $this->passwordRules(true), // <-- Pasa 'true' aquí
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'password' => $validator->errors()->first('password'),
            ]);
        }
        // --- FIN de la lógica de validación de robustez de contraseña ---

        $user = User::where(Fortify::username(), $request->{Fortify::username()})->first();

        // Registro de intento de acceso (ajustado para ser más robusto si el usuario no existe)
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
            // Disparar un evento de fallo de autenticación
            event(new Failed('web', $user, $request->merge([
                'password' => '****',
            ])->all()));

            // Redirigir al login con el mensaje de inactivo
            return redirect()->route('login')->withInput($request->only(Fortify::username()))->with('inactivo', true);
        }

        // Si el usuario está activo o no se encontró, Fortify continúa con el siguiente paso del pipeline.
        // Aquí es donde el pipeline de Fortify continuará con la verificación de contraseña
        // a través de la acción Laravel\Fortify\Actions\AttemptToAuthenticate::class
        return $next($request);
    }
}
