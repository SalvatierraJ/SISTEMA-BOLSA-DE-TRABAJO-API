<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostulacionTrabajoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $estudiante;
    public $trabajo;
    public $cvPath;

    public function __construct($estudiante, $trabajo, $cvPath)
    {
        $this->estudiante = $estudiante;
        $this->trabajo = $trabajo;
        $this->cvPath = $cvPath;
    }

    public function build()
    {
        return $this->subject("Postulación para el trabajo: {$this->trabajo->Titulo}")
                    ->markdown('emails.postulacion');
    }
}
