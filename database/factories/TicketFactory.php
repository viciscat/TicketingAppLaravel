<?php

namespace Database\Factories;

use App\Enums\TicketKind;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<Ticket> */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $projectId = Project::inRandomOrder()->first()->id;
        return [
            'local_id' => 0,
            'title' => $this->faker->text(64),
            'status' => $this->faker->randomElement(TicketStatus::cases()),
            'type' => $this->faker->randomElement(TicketType::cases()),
            'kind' => $this->faker->randomElement(TicketKind::cases()),
            'priority' => $this->faker->randomElement(TicketPriority::cases()),
            'description' => $this->faker->text(500),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'project_id' => $projectId,
            'created_by' => User::inRandomOrder()
                ->where("role", "!=", UserRole::CLIENT)
                ->whereRelation("projects", "id", "=", $projectId) // FIXME randomly fails
                ->first()->id,
        ];
    }
}
