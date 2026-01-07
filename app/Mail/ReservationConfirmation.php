<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Reserva #' . str_pad($this->reservation->id, 6, '0', STR_PAD_LEFT) . ' - Hotel Dios Padre',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_confirmation',
        );
    }
}