<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaRevisionPendienteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $item; // Puede ser un Producto o un Boletin
    public $itemTipo; // Para saber si es 'Noticia' o 'Boletín'

    /**
     * Create a new message instance.
     */
    public function __construct($item, $itemTipo)
    {
        $this->item = $item;
        $this->itemTipo = $itemTipo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva ' . $this->itemTipo . ' Pendiente de Revisión',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.nueva_revision_pendiente', // Crearemos esta vista de correo
            with: [
                'item' => $this->item,
                'itemTipo' => $this->itemTipo,
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