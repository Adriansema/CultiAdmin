<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Noticia; // Importar el modelo Noticia

class NoticiaEstadoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La instancia de la noticia.
     *
     * @var \App\Models\Noticia
     */
    public $noticia;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct(Noticia $noticia)
    {
        $this->noticia = $noticia;
    }

    /**
     * Obtiene el sobre del mensaje.
     */
    public function envelope(): Envelope
    {
        $subject = '';
        if ($this->noticia->estado === 'aprobado') {
            $subject = '¡Tu noticia ha sido APROBADA!';
        } elseif ($this->noticia->estado === 'rechazado') {
            $subject = 'Tu noticia ha sido RECHAZADA';
        } else {
            $subject = 'Actualización de estado de tu noticia';
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.noticias_estado', // Ruta de la vista Blade
            with: [
                'noticia' => $this->noticia, // Pasa la instancia de la noticia a la vista
            ],
        );
    }

    /**
     * Obtiene los adjuntos para el mensaje.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
