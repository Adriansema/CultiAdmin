<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Producto;
use App\Models\Boletin;

class NuevaRevisionPendienteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La instancia del elemento (Producto o Boletin) pendiente de revision.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $item; // Este contendra el objeto Producto o Boletin

    /**
     * El tipo de elemento a revisar (ej. 'Cafe', 'Mora', 'Boletin').
     *
     * @var string
     */
    public $itemTipo;

    /**
     * Crea una nueva instancia de mensaje.
     *
     * @param \Illuminate\Database\Eloquent\Model $item       El objeto del elemento (Producto o Boletin).
     * @param string|null                         $itemTipo   El tipo de elemento (ej. 'Cafe', 'Mora', 'Boletin').
     */
    public function __construct($item, ?string $itemTipo = null) // Eliminamos $detallesItem del constructor
    {
        $this->item = $item;
        // Si $itemTipo no se proporciona, intenta obtenerlo del campo 'tipo' del item,
        // o usa un valor por defecto.
        $this->itemTipo = $itemTipo ?? ($item->tipo ?? 'Elemento');
    }

    /**
     * Obtiene la envolvente del mensaje.
     */
    public function envelope(): Envelope
    {
        // Un asunto claro para que el operario identifique rapidamente de que se trata
        $subject = "!Nueva Revision Pendiente! - " . ucfirst($this->itemTipo);

        // Anade el ID del elemento al asunto si esta disponible
        if (isset($this->item->id)) {
            $subject .= " (ID: " . $this->item->id . ")";
        }

        return new Envelope(
            subject: $subject,
            // Opcional: Define el remitente si es diferente al configurado en .env
            // from: config('mail.from.address', 'notificaciones@tudominio.com'),
        );
    }

    /**
     * Obtiene la definicion del contenido del mensaje.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.nueva_revision_pendiente', // La vista Blade actualizada
            with: [
                'item' => $this->item,     // Solo pasamos el item principal
                'itemTipo' => $this->itemTipo, // Y el tipo de item
                // Ya no necesitamos pasar 'detallesItem' aqui porque la vista no lo usa
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
        return []; // Los adjuntos se gestionan en la vista si el 'item' tiene una ruta de archivo/imagen.
    }
}