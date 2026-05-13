<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'created_by_id' => $creator->id,
            'assignee_id' => $assignee->id,
            'title' => 'Ship task API',
            'description' => 'Create the task management endpoints.',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDay()->toDateString(),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Ship task API')
            ->assertJsonPath('data.assignee.id', $assignee->id);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Ship task API',
            'created_by_id' => $creator->id,
            'assignee_id' => $assignee->id,
        ]);
    }

    public function test_user_can_update_task_status(): void
    {
        $task = Task::factory()->create(['status' => 'pending', 'completed_at' => null]);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'status' => 'completed',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');

        $this->assertNotNull($task->refresh()->completed_at);
    }

    public function test_user_can_view_task_summary(): void
    {
        Task::factory()->count(2)->create(['status' => 'pending', 'priority' => 'medium']);
        Task::factory()->create(['status' => 'completed', 'priority' => 'high', 'completed_at' => now()]);

        $response = $this->getJson('/api/tasks/summary');

        $response
            ->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonPath('by_status.pending', 2)
            ->assertJsonPath('by_status.completed', 1)
            ->assertJsonPath('by_priority.medium', 2);
    }
}
