<?php

namespace App\Repositories\Api;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskRepository
{
    public function getAll(int $perPage = 3): LengthAwarePaginator
    {
        return Task::paginate($perPage);
    }

    public function find(int $id)
    {
        try {
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $task = new Task();
            $task->error = 'Task not found';

            return $task;
        }

        return $task;
    }

    public function filter(object $request): array | Collection
    {
        $status = $request->input('status');
        $priority_from = $request->input('priority_from');
        $priority_to = $request->input('priority_to');
        $title = $request->input('title');
        $sort = $request->input('sort');

        return Task::query()
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
    }
}
