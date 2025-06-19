<?php

namespace App\Mail;

use App\Models\Pqrs; // Importa tu modelo Pqrs
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PqrsConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The PQRS instance.
     *
     * @var \App\Models\Pqrs
     */
    public $pqrs; // Declara una propiedad pública para pasar el objeto Pqrs a la vista

    /**
     * Create a new message instance.
     */
    public function __construct(Pqrs $pqrs) // Recibe el objeto Pqrs en el constructor
    {
        $this->pqrs = $pqrs; // Asigna el objeto Pqrs a la propiedad pública
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Recepción de tu PQR - Ref: ' . $this->pqrs->id, // Asunto del correo
            // Utiliza la configuración de Laravel para el email del remitente
            from: config('mail.from.address', 'noreply@tudominio.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pqrs_confirmation', // La vista Blade que definirá el cuerpo del correo
            with: [
                'pqrs' => $this->pqrs, // Pasa el objeto Pqrs a la vista del correo
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
        return []; // Aquí puedes añadir archivos adjuntos si los hubiera
    }
}