<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\Api\CreateTaskModelInterface;
use App\Contracts\Api\EditTaskModelInterface;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function create(CreateTaskModelInterface $model): Task
    {
        $this->validated($model);

        if (!User::find($model->getUserId())) {
            throw new Exception('User not found', 400);
        }

        $task = new Task();
        $task->status = $model->getStatus();
        $task->priority = $model->getPriority();
        $task->title = $model->getTitle();
        $task->description = $model->getDescription();
        $task->parent_id = $model->getParentId();
        $task->user_id = $model->getUserId();
        $task->save();

        return $task;
    }

    public function edit(EditTaskModelInterface $model, int $id): Task
    {
        $this->validated($model);

        $task = Task::find($id);

        if ($task->status != 'todo') {
            throw new Exception('Task completed ' . date_format($task->finished_at, 'H:i:s d.m.Y') . '. Editing is prohibited!', 400);
        }

        if (!User::find($model->getUserId())) {
            throw new Exception('User not found', 400);
        }

        if (($model->getStatus() == 'done') and (sizeof($this->checkSubTaskStatus($id, 'todo')) != 0)) {
            throw new Exception('Unfinished subtasks left', 400);
        }

        $task = Task::find($id);
        $task->status = $model->getStatus();
        $task->priority = $model->getPriority();
        $task->title = $model->getTitle();
        $task->description = $model->getDescription();
        $task->parent_id = $model->getParentId();
        $task->user_id = $model->getUserId();

        if ($model->getStatus() == 'done') {
            $task->finished_at = Carbon::now();
        }

        $task->updated_at = Carbon::now();
        $task->save();

        return $task;
    }

    public function delete(int $id)
    {
        $task = Task::find($id);

        if (!$task) {
            throw new Exception('Task not found', 404);
        }

        if ($task->status == 'done') {
            throw new Exception('You cannot delete a completed task', 404);
        }

        if (sizeof($this->checkSubTaskStatus($id, 'todo')) != 0) {
            throw new Exception('You cannot delete a task that has subtasks', 404);
        }

        unset($task);

        Task::find($id)->delete();

        return 'The task was successfully deleted';
    }

    private function checkSubTaskStatus(int $id, $taskStatus = null): Collection
    {
        if (!isset($taskStatus)) {
            return Task::query()
                ->whereNot('parent_id', '=', '0')
                ->where('parent_id', '=', $id)
                ->where('status', '=', $taskStatus)
                ->get();
        } else {
            return Task::query()
                ->whereNot('parent_id', '=', '0')
                ->where('parent_id', '=', $id)
                ->get();
        }
    }

    private function validated($model)
    {
        $validStatus = ['todo', 'done'];

        if (!in_array($model->getStatus(), $validStatus)) {
            throw new Exception('Status must be "todo" or "done"', 400);
        }

        if (($model->getPriority() < 1) or ($model->getPriority() > 5)) {
            throw new Exception('Priority can be from 1 to 5', 400);
        }
    }
}
