<?php

namespace App\Services;

use App\Jobs\SendTicketEscalationChannelJob;
use App\Models\Ticket;
use App\Models\TicketNotificationLog;
use App\Notifications\Channels\EmailEscalationChannel;
use App\Notifications\Channels\SlackEscalationChannel;
use App\Notifications\Contracts\NotificationChannel;
use Illuminate\Support\Collection;

class TicketEscalationService
{
    public function sendNotifications(Ticket $ticket, array $channels): Collection
    {
        $supportedChannels = collect($channels)
            ->unique()
            ->filter(fn (string $channel) => in_array($channel, ['email', 'slack'], true))
            ->values();

        return $supportedChannels->map(function (string $channel) use ($ticket) {
            $log = TicketNotificationLog::create([
                'ticket_id' => $ticket->id,
                'channel' => $channel,
                'status' => TicketNotificationLog::STATUS_PENDING,
                'attempts' => 0,
                'payload' => [],
            ]);

            SendTicketEscalationChannelJob::dispatch($log);

            return $log;
        });
    }

    public function resolveChannelHandler(string $channel): NotificationChannel
    {
        return match ($channel) {
            'email' => app(EmailEscalationChannel::class),
            'slack' => app(SlackEscalationChannel::class),
            default => throw new \InvalidArgumentException("Unsupported channel: {$channel}"),
        };
    }
}
