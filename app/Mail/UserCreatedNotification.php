<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
// No necesitamos Illuminate\Mail\Mailables\Markdown si no estamos usando Markdown de Laravel
// use Illuminate\Mail\Mailables\Markdown; 

class UserCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $appName;
    public $generatedPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $generatedPassword)
    {
        $this->user = $user;
        $this->appName = config('app.name');
        $this->generatedPassword = $generatedPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a ' . $this->appName . '! Tu cuenta ha sido creada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.user-created-notification', 
            with: [
                'user' => $this->user, 
                'userName' => $this->user->name, 
                'appName' => $this->appName,
                'generatedPassword' => $this->generatedPassword,
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