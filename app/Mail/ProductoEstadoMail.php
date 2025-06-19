<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Producto;

class ProductoEstadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $producto;

    /**
     * Create a new message instance.
     */
    public function __construct(Producto $producto)
    {
        $this->producto = $producto;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Se utiliza el tipo de producto (café o mora) en el asunto
        $itemTypeName = ucfirst($this->producto->tipo); // 'Café' o 'Mora'
        $subject = "Actualización de tu {$itemTypeName}: ";

        if ($this->producto->estado === 'aprobado') {
            $subject .= '¡Aprobado!';
        } elseif ($this->producto->estado === 'rechazado') {
            $subject .= '¡Rechazado!';
        } else {
            $subject .= 'Pendiente de Revisión'; // En caso de que se envíe con otro estado
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.producto_estado',
            with: [
                'producto' => $this->producto,
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