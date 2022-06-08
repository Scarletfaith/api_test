<?php

namespace App\Virtual\Api;

/**
 * @OA\Schema(
 *     description="Some request create task",
 *     type="object",
 *     title="Task update request"
 * )
 */
class TaskUpdateRequest
{
    /**
     * @OA\Property(
     *     title="Status",
     *     description="Status",
     *     format="string",
     *     example="todo"
     * )
     *
     * @var string
     */
    public $status;

    /**
     * @OA\Property(
     *     title="Priority",
     *     description="Priority",
     *     format="int64",
     *     example="5"
     * )
     *
     * @var int
     */
    public $priority;

    /**
     * @OA\Property(
     *     title="Title",
     *     description="Title",
     *     format="string",
     *     example="example task title"
     * )
     *
     * @var string
     */
    public $title;

    /**
     * @OA\Property(
     *     title="Description",
     *     description="Description",
     *     format="string",
     *     example="example task description"
     * )
     *
     * @var string
     */
    public $description;
}
