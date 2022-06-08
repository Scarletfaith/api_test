<?php

namespace App\Repositories\Api;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TaskRepository
{
    public function getAll(int $perPage = 2): LengthAwarePaginator
    {
        return Task::query()->paginate($perPage);
    }

    public function find(int $id): Builder | array | Collection | Model
    {
        return Task::query()->findOrFail($id);
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
