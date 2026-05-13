# Task Management API

A Laravel 12 REST API for a small team task-management workflow.

## Features

- Create, list, view, update, and delete tasks
- Assign tasks to team members
- Track task status and priority
- View aggregate task summaries
- Seed users and sample tasks for local testing

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The example environment is configured for MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management_api
DB_USERNAME=root
DB_PASSWORD=
```

## Seed Data

Run all seeders:

```bash
php artisan db:seed
```

The database seeders create:

- Five users with the password `password`
- Five task records across `pending`, `in_progress`, `completed`, and `cancelled`
- Assigned and unassigned tasks for summary testing

Seeders live in:

- `database/seeders/UserSeeder.php`
- `database/seeders/TaskSeeder.php`
- `database/seeders/DatabaseSeeder.php`

## API Endpoints

Postman collection:

```text
postman_collection.json
```

Interactive Swagger documentation is available after starting the server:

```text
http://localhost:8000/api/documentation
```

Regenerate the OpenAPI JSON after changing endpoint annotations:

```bash
php artisan l5-swagger:generate
```

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/api/tasks` | List tasks with optional filters |
| POST | `/api/tasks` | Create a task |
| GET | `/api/tasks/{task}` | View a task |
| PATCH/PUT | `/api/tasks/{task}` | Update a task |
| DELETE | `/api/tasks/{task}` | Delete a task |
| GET | `/api/tasks/summary` | View task counts by status, priority, and assignee |

### List Filters

`GET /api/tasks` accepts:

- `status`: `pending`, `in_progress`, `completed`, `cancelled`
- `assignee_id`: existing user ID
- `search`: title or description text
- `sort`: `created_at`, `due_date`, `priority`, `status`
- `direction`: `asc` or `desc`
- `per_page`: 1 to 100

### Create Task Example

```json
{
  "created_by_id": 1,
  "assignee_id": 2,
  "title": "Build task API",
  "description": "Create the task management endpoints.",
  "status": "pending",
  "priority": "high",
  "due_date": "2026-05-20"
}
```

## Tests

```bash
php artisan test
```
