<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\User; // ¡Esta línea es CRUCIAL y debe estar aquí!
use Illuminate\Support\Facades\Log; // Para registrar mensajes de depuración

class MarkUserOffline
{
    /**
     * Maneja el evento de cierre de sesión del usuario.
     *
     * @param  \Illuminate\Auth\Events\Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        // Registra que el Listener MarkUserOffline se ha activado.
        Log::info('MarkUserOffline Listener activado.');

        // Verifica si el evento de cierre de sesión tiene un usuario asociado.
        if ($event->user) {
            // Obtiene la instancia del usuario desde el evento.
            $user = $event->user;

            // Establece la propiedad 'is_online' del usuario a false.
            $user->is_online = false;

            try {
                // Intenta guardar los cambios en la base de datos.
                $user->save();
                // Si se guarda exitosamente, registra un mensaje de éxito.
                Log::info('MarkUserOffline: is_online guardado EXITOSAMENTE para usuario ID: ' . $user->id);
            } catch (\Exception $e) {
                // Si ocurre un error durante el guardado, registra un mensaje de error con los detalles.
                Log::error('MarkUserOffline: FALLO al guardar is_online para usuario ID: ' . $user->id . '. Error: ' . $e->getMessage());
            }
        } else {
            // Si el evento de cierre de sesión no tiene un usuario, registra una advertencia.
            Log::warning('MarkUserOffline: Evento de logout sin usuario asociado.');
        }
    }
}

