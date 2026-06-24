<?php

namespace App\Notifications\Contracts;

use App\Models\Ticket;

interface NotificationChannel
{
    /**
     * Send a notification payload for a ticket.
     *
     * @throws \Throwable
     */
    public function send(Ticket $ticket, array $payload): void;
}
