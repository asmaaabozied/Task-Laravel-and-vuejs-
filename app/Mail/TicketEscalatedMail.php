<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Mail\Mailable;

class TicketEscalatedMail extends Mailable
{
    public function __construct(public Ticket $ticket)
    {
    }

    public function build(): self
    {
        return $this->subject("Ticket #{$this->ticket->id} Escalated")
            ->markdown('emails.ticket_escalated', [
                'ticket' => $this->ticket,
            ]);
    }
}
