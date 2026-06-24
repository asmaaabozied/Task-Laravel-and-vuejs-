<?php

namespace Tests\Feature;

use App\Jobs\SendTicketEscalationChannelJob;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TicketEscalationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_escalation_dispatches_notification_jobs(): void
    {
        Queue::fake();

        $ticket = Ticket::factory()->create([
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->postJson("/api/tickets/{$ticket->id}/escalate", []);

        $response->assertOk();
        $this->assertSame(Ticket::STATUS_ESCALATED, $ticket->fresh()->status);

        Queue::assertPushed(SendTicketEscalationChannelJob::class, 2);
    }

    public function test_invalid_ticket_returns_404(): void
    {
        $response = $this->postJson('/api/tickets/999/escalate', []);

        $response->assertNotFound();
    }
}
