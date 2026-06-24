<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class TicketPageController
{
    public function show(Ticket $ticket)
    {
        return view('ticket-escalate', [
            'ticket' => $ticket,
        ]);
    }
}
