<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class TaskSummaryController extends Controller
{
    #[OA\Get(
        path: '/api/tasks/summary',
        operationId: 'taskSummary',
        summary: 'View task summary counts',
        tags: ['Tasks'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task summary counts',
                content: new OA\JsonContent(ref: '#/components/schemas/TaskSummary')
            ),
        ]
    )]
    public function __invoke(): JsonResponse
    {
        $byStatus = Task::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $byPriority = Task::query()
            ->select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority');

        $byAssignee = Task::query()
            ->leftJoin('users', 'tasks.assignee_id', '=', 'users.id')
            ->selectRaw("COALESCE(users.name, 'Unassigned') as assignee, count(*) as total")
            ->groupBy('users.name')
            ->orderBy('assignee')
            ->get();

        return response()->json([
            'total' => Task::query()->count(),
            'overdue' => Task::query()
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->whereDate('due_date', '<', now()->toDateString())
                ->count(),
            'by_status' => $byStatus,
            'by_priority' => $byPriority,
            'by_assignee' => $byAssignee,
        ]);
    }
}
