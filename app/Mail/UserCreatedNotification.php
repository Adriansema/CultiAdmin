<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Markdown; // Importa Markdown para usar la vista Blade de Markdown

class UserCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $appName;
    public $generatedPassword; // Nueva propiedad para la contraseña generada

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $generatedPassword) // Añade $generatedPassword al constructor
    {
        $this->user = $user;
        $this->appName = config('app.name'); // Obtiene el nombre de la aplicación
        $this->generatedPassword = $generatedPassword; // Asigna la contraseña
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu cuenta ha sido creada en ' . $this->appName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-created-notification', // Usa tu vista markdown
            with: [
                'userName' => $this->user->name,
                'appName' => $this->appName,
                'generatedPassword' => $this->generatedPassword, // Pasa la contraseña a la vista
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}