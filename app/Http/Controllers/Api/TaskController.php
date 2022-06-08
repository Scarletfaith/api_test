<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Models\Task;
use App\Repositories\Api\TaskRepository;
use App\Services\Api\TaskService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    private $taskServices;
    private $taskRepository;

    public function __construct(TaskService $TaskService, TaskRepository $TaskRepository)
    {
        $this->taskServices = $TaskService;
        $this->taskRepository = $TaskRepository;
    }

    /**
     * @OA\Get(
     *     path="/tasks",
     *     operationId="tasksAll",
     *     tags={"TODO project"},
     *     summary="Get all tasks",
     * @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number",
     *         required=false,
     * @OA\Schema(
     *              type="integer",
     *         )
     *     ),
     * @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     * @OA\Response(
     *         response="404",
     *         description="Tasks not found"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $tasks = $this->taskRepository->getAll();
        return response()->json($tasks);
    }

    /**
     * @OA\Get(
     *     path="/tasks/{id}",
     *     operationId="taskGet",
     *     tags={"TODO project"},
     *     summary="Get task by ID",
     *     security={
     *       {"api_key": {}},
     *     },
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of task",
     *         required=true,
     *         example="1",
     * @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     * @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     * @OA\Response(
     *         response="404",
     *         description="This task not found"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        return response()->json($task);
    }

    /**
     * @OA\Post(
     *     path="/tasks/findByFilter",
     *     operationId="taskFilter",
     *     tags={"TODO project"},
     *     summary="Task filter",
     * @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="The status of task",
     *         required=true,
     * @OA\Schema(
     *             type="string",
     *             enum={"todo", "done"}
     *         )
     *     ),
     * @OA\Parameter(
     *         name="priority_from",
     *         in="query",
     *         description="Select tasks by priority from",
     *         required=false,
     * @OA\Schema(
     *             type="string",
     *             enum={"1", "2", "3", "4", "5"}
     *         )
     *     ),
     * @OA\Parameter(
     *         name="priority_to",
     *         in="query",
     *         description="Select tasks by priority to",
     *         required=false,
     * @OA\Schema(
     *             type="string",
     *             enum={"1", "2", "3", "4", "5"}
     *         )
     *     ),
     * @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="The status of task",
     *         required=false,
     * @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Select sort",
     *         required=false,
     * @OA\Schema(
     *             type="string",
     *             enum={"created_at", "finished_at", "priority"}
     *         )
     *     ),
     * @OA\Response(
     *         response="200",
     *         description="OK"
     *     )
     * )
     */
    public function findByFilter(Request $request): JsonResponse
    {
        $tasks = $this->taskRepository->filter($request);

        return response()->json($tasks);
    }

    /**
     * @OA\Post(
     *     path="/tasks",
     *     operationId="taskCreate",
     *     tags={"TODO project"},
     *     summary="Create new task",
     *     security={
     *       {"api_key": {}},
     *     },
     * @OA\Response(
     *         response="201",
     *         description="Task created"
     *     ),
     * @OA\Response(
     *         response="400",
     *         description="Bad request"
     *     ),
     * @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(ref="#/components/schemas/TaskStoreRequest")
     *     )
     * )
     */
    public function store(StoreRequest $request)
    {
        $task = $this->taskServices->create($request);

        if ($task->validated == 'true') {
            return response('', 201);
        } else {
            return response($task, 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/tasks/{id}",
     *     operationId="taskEdit",
     *     tags={"TODO project"},
     *     summary="Update task by ID",
     *     security={
     *       {"api_key": {}},
     *     },
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of task",
     *         required=true,
     * @OA\Schema(
     *              type="integer",
     *         )
     *     ),
     * @OA\Response(
     *          response="200",
     *          description="OK"
     *     ),
     * @OA\Response(
     *         response="400",
     *         description="Bad request"
     *     ),
     * @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     * @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskStoreRequest")
     *     )
     * )
     */
    public function update(UpdateRequest $request, int $id): Application | ResponseFactory | Response
    {
        $task = $this->taskServices->edit($request, $id);
        if ($task->validated == 'true') {
            return response('', 200);
        } else {
            return response($task, 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/tasks/{id}",
     *     operationId="taskDelete",
     *     tags={"TODO project"},
     *     summary="Delete task by ID",
     *     security={
     *       {"api_key": {}},
     *     },
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of task",
     *         required=true,
     *         example="1",
     * @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     * @OA\Response(
     *         response="202",
     *         description="Deleted",
     *     ),
     * )
     */
    public function destroy(int $id)
    {
        $task = $this->taskServices->delete($id);
        return response()->json($task);
    }
}
