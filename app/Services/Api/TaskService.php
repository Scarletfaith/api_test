<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\Api\CreateTaskModelInterface;
use App\Contracts\Api\EditTaskModelInterface;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    public function create(CreateTaskModelInterface $model): Task
    {
        $validated = $this->validated($model->getStatus(), $model->getPriority());
        $task = new Task();

        if (!isset($validated->status) and !isset($validated->priority)) {
            $checkUser = $this->checkUser($model->getUserId());

            if ($checkUser) {
                $task->status = $model->getStatus();
                $task->priority = $model->getPriority();
                $task->title = $model->getTitle();
                $task->description = $model->getDescription();
                $task->parent_id = $model->getParentId();
                $task->user_id = $model->getUserId();
                $task->save();
            } else {
                $task->error = 'User not found';
            }
        } else {
            $task->error = $validated;
        }

        return $task;
    }

    public function edit(EditTaskModelInterface $model, int $id): Task
    {
        $validated = $this->validated($model->getStatus(), $model->getPriority());

        if (!isset($validated->status) and !isset($validated->priority)) {
            $checkTask = $this->checkTask($id);

            if ($checkTask) {
                if ($checkTask->status == 'todo') {
                    $checkUser = $this->checkUser($model->getUserId());

                    if ($checkUser) {
                        $countTodoSubTask = sizeof($this->checkSubTaskStatus($id, 'todo'));
                        if (($model->getStatus() == 'done') and ($countTodoSubTask == 0)) {
                            $task = Task::find($id);
                            $task->status = $model->getStatus();
                            $task->priority = $model->getPriority();
                            $task->title = $model->getTitle();
                            $task->description = $model->getDescription();
                            $task->parent_id = $model->getParentId();
                            $task->user_id = $model->getUserId();
                            $task->finished_at = Carbon::now();
                            $task->updated_at = Carbon::now();
                            $task->save();
                        } elseif ($model->getStatus() == 'todo') {
                            $task = Task::find($id);
                            $task->status = $model->getStatus();
                            $task->priority = $model->getPriority();
                            $task->title = $model->getTitle();
                            $task->description = $model->getDescription();
                            $task->parent_id = $model->getParentId();
                            $task->user_id = $model->getUserId();
                            $task->updated_at = Carbon::now();
                            $task->save();
                        } else {
                            $task = new Task();
                            $task->error = $countTodoSubTask . ' unfinished subtasks left';
                        }
                    } else {
                        $task = new Task();
                        $task->error = 'User not found';
                    }
                } else {
                    $task = new Task();
                    $task->error = 'Task completed ' . date_format($checkTask->finished_at, 'H:i:s d.m.Y') . '. Editing is prohibited!';
                }
            } else {
                $task = new Task();
                $task->error = 'Task not found';
            }
        } else {
            $task = new Task();
            $task->error = $validated;
        }

        return $task;
    }

    public function delete(int $id)
    {
        $task = new Task();
        $checkTask = $this->checkTask($id);

        if ($checkTask) {
            if ($checkTask->status == 'todo') {
                $checkSubTasks = sizeof($this->checkSubTaskStatus($id, 'todo'));

                if ($checkSubTasks == 0) {
                    Task::find($id)->delete();
                    $task->success = 'The task was successfully deleted';
                } else {
                    $task->error = 'You cannot delete a task that has subtasks';
                }
            } else {
                $task->error = 'You cannot delete a completed task';
            }
        } else {
            $task->error = 'Task not found';
        }

        return $task;
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

    private function checkTask(int $id)
    {
        try {
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException) {
            return false;
        }

        return $task;
    }

    private function checkUser(int $id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException) {
            return false;
        }

        return $user;
    }

    private function validated(string $status, int $priority): object
    {
        $validated = (object)[];
        $valid_status = ['todo', 'done'];

        if (!in_array($status, $valid_status)) {
            $validated->status = 'Status must be "todo" or "done"';
        }

        if (($priority < 1) or ($priority > 5)) {
            $validated->priority = 'Priority can be from 1 to 5';
        }

        return $validated;
    }
}
