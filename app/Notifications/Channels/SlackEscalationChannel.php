<?php

namespace App\Notifications\Channels;

use App\Models\Ticket;
use App\Notifications\Contracts\NotificationChannel;
use Illuminate\Support\Facades\Http;

class SlackEscalationChannel implements NotificationChannel
{
    public function send(Ticket $ticket, array $payload): void
    {
        $webhook = config('services.slack.webhook_url');

        if (! $webhook) {
            throw new \RuntimeException('Slack webhook URL is not configured.');
        }

        $response = Http::timeout(10)->post($webhook, [
            'text' => "Ticket #{$ticket->id} has been escalated. Subject: {$ticket->subject}. Priority: {$ticket->priority}.",
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Slack notification failed with status ' . $response->status());
        }
    }
}
