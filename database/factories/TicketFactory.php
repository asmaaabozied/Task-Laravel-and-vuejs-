<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'status' => Ticket::STATUS_OPEN,
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
        ];
    }
}
