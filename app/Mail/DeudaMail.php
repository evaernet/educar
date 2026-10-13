<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

/**
 * ¿Qué hace?: Mail del día 20 avisando al padre la deuda que le queda.
 */
class DeudaMail extends Mailable
{
    use Queueable;

    public function __construct(public string $nombrePadre, public Collection $facturas)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Educar - Tenés pagos pendientes');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.deuda', with: [
            'nombrePadre' => $this->nombrePadre,
            'facturas' => $this->facturas,
            'total' => $this->facturas->sum(fn ($f) => $f->saldoPendiente()),
        ]);
    }
}
