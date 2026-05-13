<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Seed sample work items across statuses and assignees.
     */
    public function run(): void
    {
        $users = User::query()->orderBy('id')->get();

        if ($users->isEmpty()) {
            return;
        }

        $creator = $users->first();

        $tasks = [
            [
                'title' => 'Define API response contract',
                'description' => 'Document the shape used by task list, detail, and summary endpoints.',
                'status' => 'completed',
                'priority' => 'high',
                'due_date' => now()->subDays(2)->toDateString(),
                'completed_at' => now()->subDay(),
                'assignee_id' => $users->get(1)?->id,
            ],
            [
                'title' => 'Build task creation endpoint',
                'description' => 'Validate required fields and persist creator and optional assignee.',
                'status' => 'in_progress',
                'priority' => 'urgent',
                'due_date' => now()->addDays(2)->toDateString(),
                'completed_at' => null,
                'assignee_id' => $users->get(2)?->id,
            ],
            [
                'title' => 'Prepare QA seed dataset',
                'description' => 'Create repeatable users and tasks for local API testing.',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addWeek()->toDateString(),
                'completed_at' => null,
                'assignee_id' => $users->get(3)?->id,
            ],
            [
                'title' => 'Review cancelled task handling',
                'description' => 'Confirm cancelled tasks are still visible in summary counts.',
                'status' => 'cancelled',
                'priority' => 'low',
                'due_date' => null,
                'completed_at' => null,
                'assignee_id' => null,
            ],
            [
                'title' => 'Optimize task filters',
                'description' => 'Add indexed filters for status and assignee lookup paths.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(10)->toDateString(),
                'completed_at' => null,
                'assignee_id' => $users->get(1)?->id,
            ],
        ];

        foreach ($tasks as $task) {
            Task::query()->updateOrCreate(
                ['title' => $task['title']],
                [
                    ...$task,
                    'created_by_id' => $creator->id,
                ],
            );
        }
    }
}
