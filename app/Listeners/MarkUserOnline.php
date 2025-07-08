<?php

    namespace App\Listeners;

    use Illuminate\Auth\Events\Login;
    use App\Models\User; // ¡Esta línea es CRUCIAL y debe estar aquí!
    use Illuminate\Support\Facades\Log; // Para registrar mensajes de depuración

    class MarkUserOnline
    {
        /**
         * Maneja el evento de inicio de sesión del usuario.
         *
         * @param  \Illuminate\Auth\Events\Login  $event
         * @return void
         */
        public function handle(Login $event)
        {
            Log::info('MarkUserOnline Listener activado.');

            if ($event->user) {
                $user = $event->user;

                // *** LÍNEA DE DEPURACIÓN CLAVE ***
                // Esto registrará la clase exacta del objeto $user en el log de Laravel.
                Log::info('Clase del usuario en MarkUserOnline: ' . get_class($user));

                $user->is_online = true;

                try {
                    $user->save();
                    Log::info('MarkUserOnline: is_online guardado EXITOSAMENTE para usuario ID: ' . $user->id);
                } catch (\Exception $e) {
                    Log::error('MarkUserOnline: FALLO al guardar is_online para usuario ID: ' . $user->id . '. Error: ' . $e->getMessage());
                }
            } else {
                Log::warning('MarkUserOnline: Evento de login sin usuario asociado.');
            }
        }
    }
    