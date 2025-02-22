<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints of Users",
 * )
 */

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class UserController
{
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve users",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Failed to retrieve users"),
     *             @OA\Property(property="code", type="string", example="DATABASE_ERROR"),
     *             @OA\Property(property="message", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function getAll()
    {
        try {
            $users = User::all();
            return successResponse("Users retrieved successfully", $users);
        } catch (\Exception $e) {
            LogHelper::logError($e, ['controller' => 'UserController', 'function' => 'getAll']);
            return errorResponse("Failed to retrieve users", "DATABASE_ERROR", $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get user by ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the user to retrieve"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User retrieved successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="User not found"),
     *             @OA\Property(property="code", type="string", example="USER_NOT_FOUND"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Failed to retrieve user"),
     *             @OA\Property(property="code", type="string", example="DATABASE_ERROR"),
     *             @OA\Property(property="message", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function getById($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return errorResponse("User not found", "USER_NOT_FOUND", "User not found", 404);
            }
            return successResponse("User retrieved successfully", $user);
        } catch (\Exception $e) {
            LogHelper::logError($e, ['controller' => 'UserController', 'function' => 'getById']);
            return errorResponse("Failed to retrieve user", "DATABASE_ERROR", $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/add",
     *     summary="Add a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="code", type="string", example="VALIDATION_ERROR"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create user",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to create user"),
     *             @OA\Property(property="code", type="string", example="DATABASE_ERROR"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function add(Request $request)
    {
        try {
            // Validate Request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return errorResponse("Validation failed", "VALIDATION_ERROR", $validator->errors(), 400);
            }

            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            return successResponse("User created successfully", $user, 201);
        } catch (\Exception $e) {
            LogHelper::logError($e, ['controller' => 'UserController', 'function' => 'add']);
            return errorResponse("Failed to create user", "DATABASE_ERROR", $e->getMessage(), 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the user to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="User not found"),
     *             @OA\Property(property="code", type="string", example="USER_NOT_FOUND"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Failed to update user"),
     *             @OA\Property(property="code", type="string", example="DATABASE_ERROR"),
     *             @OA\Property(property="message", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return errorResponse("User not found", "USER_NOT_FOUND", "User not found", 404);
            }

            // Validate Request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($id) // Ignore the current user's email
                ]
            ]);

            if ($validator->fails()) {
                return errorResponse("Validation failed", "VALIDATION_ERROR", $validator->errors(), 400);
            }

            $user->update($request->only(['name', 'email']));

            return successResponse("User updated successfully", $user);
        } catch (\Exception $e) {
            LogHelper::logError($e, ['controller' => 'UserController', 'function' => 'update']);
            return errorResponse("Failed to update user", "DATABASE_ERROR", $e->getMessage(), 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         description="ID of the user to delete"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="User not found"),
     *             @OA\Property(property="code", type="string", example="USER_NOT_FOUND"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete user",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to delete user"),
     *             @OA\Property(property="code", type="string", example="DATABASE_ERROR"),
     *             @OA\Property(property="message", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return errorResponse("User not found", "USER_NOT_FOUND", "User not found", 404);
            }

            $user->delete();

            return successResponse("User deleted successfully");
        } catch (\Exception $e) {
            LogHelper::logError($e, ['controller' => 'UserController', 'function' => 'delete']);
            return errorResponse("Failed to delete user", "DATABASE_ERROR", $e->getMessage(), 500);
        }
    }

    // Truncate Users
    public function truncateAll()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            User::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return successResponse("User table truncated");
        } catch (\Exception $e) {
            LogHelper::logError($e, ['controller' => 'UserController', 'function' => 'truncateAll']);
            return errorResponse("Failed to truncate users", "DATABASE_ERROR", $e->getMessage(), 500);
        }
    }
}
