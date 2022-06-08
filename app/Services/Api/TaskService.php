<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\Api\CreateTaskModelInterface;
use App\Contracts\Api\EditTaskModelInterface;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function create(CreateTaskModelInterface $model): Task
    {
        $validated = $this->validated($model->getStatus(), $model->getPriority());
        $task = new Task();
        if (($validated->status == 'true') and ($validated->priority == 'true')) {
            $task->status = $model->getStatus();
            $task->priority = $model->getPriority();
            $task->title = $model->getTitle();
            $task->description = $model->getDescription();
            $task->parent_id = $model->getParentId();
            $task->user_id = $model->getUserId();
            $task->save();
            $task->validated = true;
        } else {
            $task->validated = $validated;
        }

        return $task;
    }

    public function edit(EditTaskModelInterface $model, int $id): Task
    {
        $validated = $this->validated($model->getStatus(), $model->getPriority());
        $task = Task::query()->find($id);
        if (($validated->status == 'true') and ($validated->priority == 'true')) {
            $countTodoSubTask = sizeof($this->checkSubTaskStatus($id, 'todo'));
            $taskStatus = $this->checkTaskStatus($id);
            if (($countTodoSubTask == 0) and ($taskStatus->status == 'todo')) {
                $task->status = $model->getStatus();
                $task->priority = $model->getPriority();
                $task->title = $model->getTitle();
                $task->description = $model->getDescription();
                if ($model->getStatus() == 'done') {
                    $task->finished_at = Carbon::now();
                }
                $task->updated_at = Carbon::now();
                $task->save();
                $task->validated = true;
            } else {
                if ($taskStatus->status == 'done') {
                    $task->validated = 'Task completed ' . date_format($taskStatus->finished_at, 'H:i:s d.m.Y');
                } else {
                    $task->validated = $countTodoSubTask . ' unfinished subtasks left';
                }
            }
        } else {
            $task->validated = $validated;
        }

        return $task;
    }

    public function delete(int $id)
    {
        $checkStatus = $this->checkTaskStatus($id);
        $checkSubTasks = sizeof($this->checkSubTaskStatus($id, 'todo'));
        if ($checkStatus->status == 'todo') {
            if ($checkSubTasks == 0) {
                return Task::query()->find($id)->delete();
            } else {
                return 'You cannot delete a task that has subtasks';
            }
        } else {
            return 'You cannot delete a completed task';
        }
    }

    private function checkSubTaskStatus(int $id, $todoStatus = null): array | Collection
    {
        if (!isset($todoStatus)) {
            return Task::query()
                ->whereNot('parent_id', '=', '0')
                ->where('parent_id', '=', $id)
                ->where('status', '=', $todoStatus)
                ->get();
        } else {
            return Task::query()
                ->whereNot('parent_id', '=', '0')
                ->where('parent_id', '=', $id)
                ->get();
        }
    }

    private function checkTaskStatus(int $id)
    {
        return Task::query()->findOrFail($id);
    }

    private function validated(string $status, int $priority)
    {
        $validated = (object)[];
        $valid_status = array('todo', 'done');
        if (in_array($status, $valid_status)) {
            $validated->status = true;
        } else {
            $validated->status = 'Status must be "todo" or "done"';
        }

        if (($priority >= 1) and ($priority <= 5)) {
            $validated->priority = true;
        } else {
            $validated->priority = 'Priority can be from 1 to 5';
        }

        return $validated;
    }
}
