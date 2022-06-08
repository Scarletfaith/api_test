<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *     title="Laravel Swagger API documentation",
 *     version="1.0.0",
 * @OA\Contact(
 *          email="localhost@localhost"
 *     ),
 * @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\Tag(
 *     name="TODO project",
 *     description="My test TODO API project",
 * )
 * @OA\Server(
 *     description="Laravel Swagger API server",
 *     url="http://127.0.0.1/api"
 * )
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     name="X-APP-ID",
 *     securityScheme="X-APP-ID"
 * )
 */
class Controller extends \App\Http\Controllers\Controller
{
}
