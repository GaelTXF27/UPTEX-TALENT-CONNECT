<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuperarPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    // Recibimos el token desde el controlador
    public function __construct($token)
    {
        $this->token = $token;
    }

    // Configuramos el asunto del correo
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de Recuperación de Contraseña - UPTeX',
        );
    }

    // Definimos qué vista HTML usará el correo
    public function content(): Content
    {
        return new Content(
            view: 'emails.recuperar',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}