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
    public function create(CreateTaskModelInterface $model)
    {
        $validated = $this->validated($model->getStatus(), $model->getPriority());
        $task = new Task();

        if (!isset($validated->status) and !isset($validated->priority)) {
            $checkUser = $this->checkUser($model->getUserId());

            if ($checkUser) {
                $checkTask = $this->checkTask($model->getParentId());

                if ($checkTask->status == 'todo') {
                    $task->status = $model->getStatus();
                    $task->priority = $model->getPriority();
                    $task->title = $model->getTitle();
                    $task->description = $model->getDescription();
                    $task->parent_id = $model->getParentId();
                    $task->user_id = $model->getUserId();
                    $task->save();

                    return response()
                        ->json($task, '201');
                } else {
                    return response()
                        ->json(['error' => 'Creation canceled. Parent task finished ' . $checkTask->finished_at], '400');
                }
            } else {
                return response()
                    ->json(['error' => 'User not found'], '400');
            }
        } else {
            return response()
                ->json(['error' => $validated], '400');
        }
    }

    public function edit(EditTaskModelInterface $model, int $id)
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

                            return response()
                                ->json($task, '201');
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

                            return response()
                                ->json($task, '201');
                        } else {
                            return response()
                                ->json(['error' => $countTodoSubTask . ' unfinished subtasks left'], '400');
                        }
                    } else {
                        return response()
                            ->json(['error' => 'User not found'], '400');
                    }
                } else {
                    return response()
                        ->json(['error' => 'Task completed ' . date_format($checkTask->finished_at, 'H:i:s d.m.Y') . '. Editing is prohibited!'], '400');
                }
            } else {
                return response()
                    ->json(['error' => 'Task not found'], '400');
            }
        } else {
            return response()
                ->json(['error' => $validated], '400');
        }
    }

    public function delete(int $id)
    {
        $checkTask = $this->checkTask($id);

        if ($checkTask) {
            if ($checkTask->status == 'todo') {
                $checkSubTasks = sizeof($this->checkSubTaskStatus($id, 'todo'));

                if ($checkSubTasks == 0) {
                    Task::find($id)->delete();

                    return response()
                        ->json(['success' => 'The task was successfully deleted'], '202');
                } else {
                    return response()
                        ->json(['error' => 'You cannot delete a task that has subtasks'], '404');
                }
            } else {
                return response()
                    ->json(['error' => 'You cannot delete a completed task'], '404');
            }
        } else {
            return response()
                ->json(['error' => 'Task not found'], '404');
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
