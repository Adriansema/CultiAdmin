<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    //app/Mail/ContactFormMail.php
    use Queueable, SerializesModels;

    public $data; // Esto recibe los datos del formulario

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Nuevo mensaje de contacto')
                    ->view('emails.contact');
    }
}
