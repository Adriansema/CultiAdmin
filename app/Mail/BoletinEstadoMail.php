<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Boletin; // Asegúrate de que el modelo Boletin esté importado correctamente

class BoletinEstadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $boletin;

    /**
     * Crea una nueva instancia de mensaje.
     */
    public function __construct(Boletin $boletin)
    {
        $this->boletin = $boletin;
    }

    /**
     * Obtiene la envolvente del mensaje.
     */
    public function envelope(): Envelope
    {
        $subject = 'Actualización del estado de tu Boletín: ';

        if ($this->boletin->estado === 'aprobado') {
            $subject .= '¡Aprobado!';
        } elseif ($this->boletin->estado === 'rechazado') {
            $subject .= '¡Rechazado!';
        } else {
            $subject .= 'Pendiente de Revisión'; // Estado por defecto si no es aprobado ni rechazado
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
            view: 'emails.boletin_estado', // Se carga la vista de email
            with: [
                'boletin' => $this->boletin,
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