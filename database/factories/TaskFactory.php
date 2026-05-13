<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(Task::STATUSES);

        return [
            'created_by_id' => User::factory(),
            'assignee_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => $status,
            'priority' => fake()->randomElement(Task::PRIORITIES),
            'due_date' => fake()->optional(0.8)->dateTimeBetween('now', '+30 days')?->format('Y-m-d'),
            'completed_at' => $status === 'completed' ? fake()->dateTimeBetween('-10 days', 'now') : null,
        ];
    }
}
