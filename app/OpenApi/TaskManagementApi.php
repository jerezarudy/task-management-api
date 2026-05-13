<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Task Management API',
    description: 'REST API for creating, assigning, updating, and summarizing team tasks.'
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local development server'
)]
#[OA\Tag(
    name: 'Tasks',
    description: 'Task CRUD and summary endpoints'
)]
#[OA\Schema(
    schema: 'UserSummary',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Mara Santos'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'mara.santos@example.com'),
    ]
)]
#[OA\Schema(
    schema: 'Task',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Build task creation endpoint'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Validate required fields and persist creator and optional assignee.'),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'in_progress', 'completed', 'cancelled'], example: 'pending'),
        new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high', 'urgent'], example: 'high'),
        new OA\Property(property: 'due_date', type: 'string', format: 'date', nullable: true, example: '2026-05-20'),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true, example: null),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-13T08:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-13T08:00:00.000000Z'),
        new OA\Property(property: 'creator', ref: '#/components/schemas/UserSummary'),
        new OA\Property(property: 'assignee', ref: '#/components/schemas/UserSummary', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'TaskResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/Task'),
    ]
)]
#[OA\Schema(
    schema: 'TaskCollection',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Task')
        ),
        new OA\Property(
            property: 'links',
            type: 'object',
            properties: [
                new OA\Property(property: 'first', type: 'string', nullable: true),
                new OA\Property(property: 'last', type: 'string', nullable: true),
                new OA\Property(property: 'prev', type: 'string', nullable: true),
                new OA\Property(property: 'next', type: 'string', nullable: true),
            ]
        ),
        new OA\Property(
            property: 'meta',
            type: 'object',
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                new OA\Property(property: 'path', type: 'string', example: 'http://localhost:8000/api/tasks'),
                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                new OA\Property(property: 'to', type: 'integer', nullable: true, example: 5),
                new OA\Property(property: 'total', type: 'integer', example: 5),
            ]
        ),
    ]
)]
#[OA\Schema(
    schema: 'TaskStoreRequest',
    required: ['created_by_id', 'title'],
    properties: [
        new OA\Property(property: 'created_by_id', type: 'integer', example: 1),
        new OA\Property(property: 'assignee_id', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Build task API'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Create the task management endpoints.'),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'in_progress', 'completed', 'cancelled'], example: 'pending'),
        new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high', 'urgent'], example: 'high'),
        new OA\Property(property: 'due_date', type: 'string', format: 'date', nullable: true, example: '2026-05-20'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'TaskUpdateRequest',
    properties: [
        new OA\Property(property: 'assignee_id', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Build task API'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Create the task management endpoints.'),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'in_progress', 'completed', 'cancelled'], example: 'completed'),
        new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high', 'urgent'], example: 'high'),
        new OA\Property(property: 'due_date', type: 'string', format: 'date', nullable: true, example: '2026-05-20'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The title field is required.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            )
        ),
    ]
)]
#[OA\Schema(
    schema: 'TaskSummary',
    type: 'object',
    properties: [
        new OA\Property(property: 'total', type: 'integer', example: 5),
        new OA\Property(property: 'overdue', type: 'integer', example: 1),
        new OA\Property(
            property: 'by_status',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(type: 'integer'),
            example: ['pending' => 2, 'completed' => 1]
        ),
        new OA\Property(
            property: 'by_priority',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(type: 'integer'),
            example: ['medium' => 2, 'high' => 1]
        ),
        new OA\Property(
            property: 'by_assignee',
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'assignee', type: 'string', example: 'Leo Reyes'),
                    new OA\Property(property: 'total', type: 'integer', example: 2),
                ]
            )
        ),
    ]
)]
class TaskManagementApi
{
}
