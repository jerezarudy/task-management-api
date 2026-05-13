<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TaskController extends Controller
{
    #[OA\Get(
        path: '/api/tasks',
        operationId: 'listTasks',
        summary: 'List tasks',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['pending', 'in_progress', 'completed', 'cancelled'])),
            new OA\Parameter(name: 'assignee_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ['created_at', 'due_date', 'priority', 'status'])),
            new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated task list',
                content: new OA\JsonContent(ref: '#/components/schemas/TaskCollection')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string', 'in:pending,in_progress,completed,cancelled'],
            'assignee_id' => ['sometimes', 'integer', 'exists:users,id'],
            'search' => ['sometimes', 'string', 'max:255'],
            'sort' => ['sometimes', 'string', 'in:created_at,due_date,priority,status'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $tasks = Task::query()
            ->with(['assignee', 'creator'])
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['assignee_id'] ?? null, fn ($query, $assigneeId) => $query->where('assignee_id', $assigneeId))
            ->when($validated['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($validated['sort'] ?? 'created_at', $validated['direction'] ?? 'desc')
            ->paginate($validated['per_page'] ?? 15);

        return TaskResource::collection($tasks);
    }

    #[OA\Post(
        path: '/api/tasks',
        operationId: 'createTask',
        summary: 'Create a task',
        tags: ['Tasks'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TaskStoreRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Task created',
                content: new OA\JsonContent(ref: '#/components/schemas/TaskResource')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] ??= 'pending';
        $data['priority'] ??= 'medium';
        $data['completed_at'] = $data['status'] === 'completed' ? now() : null;

        $task = Task::query()->create($data)->load(['assignee', 'creator']);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/tasks/{task}',
        operationId: 'showTask',
        summary: 'View a task',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task details',
                content: new OA\JsonContent(ref: '#/components/schemas/TaskResource')
            ),
            new OA\Response(response: 404, description: 'Task not found'),
        ]
    )]
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load(['assignee', 'creator']));
    }

    #[OA\Patch(
        path: '/api/tasks/{task}',
        operationId: 'updateTask',
        summary: 'Update a task',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TaskUpdateRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task updated',
                content: new OA\JsonContent(ref: '#/components/schemas/TaskResource')
            ),
            new OA\Response(response: 404, description: 'Task not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    #[OA\Put(
        path: '/api/tasks/{task}',
        operationId: 'replaceTask',
        summary: 'Replace task fields',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TaskUpdateRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task updated',
                content: new OA\JsonContent(ref: '#/components/schemas/TaskResource')
            ),
            new OA\Response(response: 404, description: 'Task not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $data = $request->validated();

        if (($data['status'] ?? null) === 'completed' && $task->status !== 'completed') {
            $data['completed_at'] = now();
        }

        if (($data['status'] ?? null) !== null && $data['status'] !== 'completed') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return new TaskResource($task->refresh()->load(['assignee', 'creator']));
    }

    #[OA\Delete(
        path: '/api/tasks/{task}',
        operationId: 'deleteTask',
        summary: 'Delete a task',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Task deleted'),
            new OA\Response(response: 404, description: 'Task not found'),
        ]
    )]
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(status: 204);
    }
}
