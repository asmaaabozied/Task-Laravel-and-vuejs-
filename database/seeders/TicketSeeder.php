<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        Ticket::factory()->count(5)->create();

        // Create a predictable ticket with id=1 if none exists
        if (! Ticket::find(1)) {
            Ticket::create([
                'subject' => 'Seeded ticket #1',
                'description' => 'This ticket was created by TicketSeeder',
                'priority' => 'High',
                'status' => Ticket::STATUS_OPEN,
                'customer_name' => 'Seed Customer',
                'customer_email' => 'customer@example.com',
            ]);
        }
    }
}
