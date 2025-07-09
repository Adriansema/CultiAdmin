<?php

namespace App\Notifications; 

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class CustomResetPassword extends Notification 
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The callback that should be used to create the reset password URL.
     *
     * @var (\Closure(mixed, string): string)|null
     */
    public static $createUrlCallback;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var (\Closure(mixed, string): \Illuminate\Notifications\Messages\MailMessage|\Illuminate\Contracts\Mail\Mailable)|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     */
    public function __construct(#[\SensitiveParameter] $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        // Importante: Pasamos $notifiable tambien a buildMailMessage para poder acceder al nombre en la vista
        return $this->buildMailMessage($this->resetUrl($notifiable), $notifiable); 
    }

     /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @param  mixed  $notifiable // <-- Anadimos este parametro para tener el usuario disponible
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url, $notifiable) 
    {
        return (new MailMessage)
            ->subject(Lang::get('Restablecimiento de Contrasena para Cultiva SENA')) 
            ->view('emails.auth.reset', [ 
                'url' => $url,
                'token' => $this->token,
                'user' => $notifiable, 
                'expire_minutes' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
            ]);
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        // Asegurate de que APP_URL en tu .env este configurado correctamente para URLs absolutas.
        return URL::to(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false)); // El 'false' al final hace la URL relativa. Puedes quitarlo para URL absoluta si APP_URL no esta configurado.
    }

    /**
     * Set a callback that should be used when creating the reset password button URL.
     *
     * @param  \Closure(mixed, string): string  $callback
     * @return void
     */
    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure(mixed, string): (\Illuminate\Notifications\Messages\MailMessage|\Illuminate\Contracts\Mail\Mailable)  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
