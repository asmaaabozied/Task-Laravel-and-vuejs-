<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketEscalationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TicketEscalationController
{
    public function __invoke(Request $request, Ticket $ticket, TicketEscalationService $escalationService): JsonResponse
    {
        $payload = $request->validate([
            'channels' => ['sometimes', 'array'],
            'channels.*' => ['string', 'in:email,slack'],
        ]);

        $channels = $payload['channels'] ?? ['email', 'slack'];

        $ticket->update([
            'status' => Ticket::STATUS_ESCALATED,
            'escalated_at' => now(),
        ]);

        $notificationLogs = $escalationService->sendNotifications($ticket, $channels);

        return response()->json([
            'ticket' => $ticket->refresh(),
            'notifications' => $notificationLogs,
        ]);
    }
}
