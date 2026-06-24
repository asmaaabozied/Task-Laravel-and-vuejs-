<?php

namespace App\Jobs;

use App\Models\TicketNotificationLog;
use App\Services\TicketEscalationService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendTicketEscalationChannelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public TicketNotificationLog $notificationLog)
    {
    }

    public function handle(TicketEscalationService $escalationService): void
    {
        $log = $this->notificationLog->fresh();
        $log->increment('attempts');
        $log->status = TicketNotificationLog::STATUS_PENDING;
        $log->save();

        $channelHandler = $escalationService->resolveChannelHandler($log->channel);
        $channelHandler->send($log->ticket, [
            'ticket_id' => $log->ticket->id,
            'subject' => $log->ticket->subject,
        ]);

        $log->update([
            'status' => TicketNotificationLog::STATUS_SUCCESS,
            'sent_at' => now(),
            'last_error' => null,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $log = $this->notificationLog->fresh();
        $log->update([
            'status' => TicketNotificationLog::STATUS_FAILED,
            'last_error' => $exception->getMessage(),
        ]);
    }
}
