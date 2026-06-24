<?php

namespace App\Notifications\Channels;

use App\Models\Ticket;
use App\Mail\TicketEscalatedMail;
use App\Notifications\Contracts\NotificationChannel;
use Illuminate\Support\Facades\Mail;

class EmailEscalationChannel implements NotificationChannel
{
    public function send(Ticket $ticket, array $payload): void
    {
        $recipient = $ticket->customer_email ?: config('mail.from.address');

        if (! $recipient) {
            throw new \RuntimeException('No email recipient configured for ticket escalation.');
        }

        Mail::to($recipient)->send(new TicketEscalatedMail($ticket));

        if (count(Mail::failures()) > 0) {
            throw new \RuntimeException('Email delivery failed for escalated ticket.');
        }
    }
}
