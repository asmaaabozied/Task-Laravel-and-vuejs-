<?php

namespace Tests\Unit;

use App\Jobs\SendTicketEscalationChannelJob;
use App\Models\Ticket;
use App\Models\TicketNotificationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendTicketEscalationChannelJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_channel_job_marks_notification_success(): void
    {
        Mail::fake();

        $ticket = Ticket::factory()->create();
        $log = TicketNotificationLog::create([
            'ticket_id' => $ticket->id,
            'channel' => 'email',
            'status' => TicketNotificationLog::STATUS_PENDING,
            'attempts' => 0,
            'payload' => [],
        ]);

        $job = new SendTicketEscalationChannelJob($log);
        $job->handle(app('App\Services\TicketEscalationService'));

        $this->assertSame(TicketNotificationLog::STATUS_SUCCESS, $log->fresh()->status);
        $this->assertSame(1, $log->fresh()->attempts);
    }

    public function test_slack_channel_job_records_failure_when_webhook_fails(): void
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $ticket = Ticket::factory()->create();
        $log = TicketNotificationLog::create([
            'ticket_id' => $ticket->id,
            'channel' => 'slack',
            'status' => TicketNotificationLog::STATUS_PENDING,
            'attempts' => 0,
            'payload' => [],
        ]);

        $this->expectException(\RuntimeException::class);

        $job = new SendTicketEscalationChannelJob($log);
        $job->handle(app('App\Services\TicketEscalationService'));
    }
}
