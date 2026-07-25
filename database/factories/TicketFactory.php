<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return ['ticket_number' => 'TKT-'.now()->format('Ym').'-'.fake()->unique()->numerify('####'), 'user_id' => User::factory(), 'ticket_category_id' => TicketCategory::firstOrCreate(['name' => 'Hardware'])->id, 'title' => fake()->sentence(5), 'description' => fake()->paragraph(), 'location' => 'Lantai 2', 'priority' => 'sedang', 'status' => 'baru'];
    }
}
