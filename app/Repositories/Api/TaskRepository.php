<?php

namespace App\Repositories\Api;

use App\Models\Task;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getAll(int $perPage = 3): LengthAwarePaginator
    {
        return Task::paginate($perPage);
    }

    public function find(int $id): Task
    {
        $task = Task::find($id);

        if (!$task) {
            throw new Exception('Task not found', 404);
        }

        return $task;
    }

    public function filter(object $request): Collection
    {
        $status = $request->input('status');
        $priority_from = $request->input('priority_from');
        $priority_to = $request->input('priority_to');
        $title = $request->input('title');
        $sort = $request->input('sort');

        $tasks = Task::query()
            ->when(
                $status,
                function ($query, $status) {
                    $query->where('status', $status);
                }
            )
            ->when(
                $priority_from,
                function ($query, $priority_from) {
                    $query->where('priority', '>=', $priority_from);
                }
            )
            ->when(
                $priority_to,
                function ($query, $priority_to) {
                    $query->where('priority', '<=', $priority_to);
                }
            )
            ->when(
                $title,
                function ($query, $title) {
                    $query->where('title', $title);
                }
            )
            ->when(
                $sort,
                function ($query, $sort) {
                    $query->orderBy($sort);
                }
            )
            ->get();

        if ($tasks->isEmpty()) {
            throw new Exception('Tasks not found', 404);
        }

        return $tasks;
    }
}
