<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class SystemController
{
    
    /**
     * @OA\Get(
     *     path="/system/optimize-clear",
     *     summary="Optimize and clear application cache",
     *     description="This endpoint optimizes the application and clears the cache.",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="Application optimized and cache cleared",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Application optimized and cache cleared")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to clear cache",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Failed to clear cache"),
     *             @OA\Property(property="error_code", type="string", example="SERVER_ERROR"),
     *             @OA\Property(property="error_details", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function optimizeClear()
    {
        try {
            Artisan::call('optimize:clear');
            return successResponse("Application optimized and cache cleared");
        } catch (\Exception $e) {
            return errorResponse("Failed to clear cache", "SERVER_ERROR", $e->getMessage(), 500);
        }
    }
}
