<?php

namespace App\Mail;

use App\Services\FacturaPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

/**
 * ¿Qué hace?: Mail de fin de mes al padre con el total a pagar, la composición
 * de cada factura y el PDF de la factura adjunto.
 */
class FacturaMensualMail extends Mailable
{
    use Queueable;

    /** @param Collection $facturas facturas (con alumno e items) de los hijos del padre */
    public function __construct(public string $nombrePadre, public Collection $facturas)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Educar - Tu factura del mes');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.factura-mensual', with: [
            'nombrePadre' => $this->nombrePadre,
            'facturas' => $this->facturas,
            'total' => $this->facturas->sum(fn ($f) => $f->saldoPendiente()),
        ]);
    }

    public function attachments(): array
    {
        return $this->facturas->map(fn ($f) => Attachment::fromData(
            fn () => FacturaPdf::factura($f),
            'factura-' . $f->periodo . '-' . $f->alumno->legajo . '.pdf'
        )->withMime('application/pdf'))->all();
    }
}
