<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Boletin;

class BoletinEstadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $boletin;

    public function __construct(Boletin $boletin)
    {
        $this->boletin = $boletin;
    }

    public function envelope(): Envelope
    {
        $subject = 'Notificación de estado de tu Boletín: ';
        if ($this->boletin->estado === 'aprobado') {
            $subject .= '¡Aprobado!';
        } elseif ($this->boletin->estado === 'rechazado') {
            $subject .= '¡Rechazado!';
        } else {
            $subject .= 'Pendiente de Revisión';
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.boletin_estado', // Crearemos esta vista
            with: [
                'boletin' => $this->boletin,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}