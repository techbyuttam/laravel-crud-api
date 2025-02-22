<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      title="Laravel CRUD API",
 *      version="1.0.0",
 *      description="This is a simple CRUD API built with Laravel.  
 *      Provides user management features like create, read, update, and delete operations.  
 *      Uses standard response formats for consistency.  
 *      Note: All data will be reset every 7 days.  
 *      Authentication is not required for testing.",
 *      @OA\Contact(
 *          email="ubhut@techbyuttam.com",
 *          name="API Support"
 *      ),
 *      @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *      )
 * )
 *
 * @OA\Server(
 *      url="https://api-crud.techbyuttam.com/api",
 *      description="Testing Server"
 * )
 */
class SwaggerController
{
    // This controller is just for Swagger metadata, no functions needed.
}
