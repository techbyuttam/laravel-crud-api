<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

/**
 * @OA\Schema(
 *     schema="Log",
 *     @OA\Property(property="timestamp", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
 *     @OA\Property(property="level", type="string", example="ERROR"),
 *     @OA\Property(property="api", type="string", example="http://example.com/api/logs"),
 *     @OA\Property(property="method", type="string", example="GET"),
 *     @OA\Property(property="controller", type="string", example="LogController"),
 *     @OA\Property(property="function", type="string", example="getLogsByDate"),
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(property="file", type="string", example="/path/to/file.php"),
 *     @OA\Property(property="line", type="integer", example=123),
 *     @OA\Property(property="user_id", type="string", example="1 or Guest")
 * )
 */

class LogController
{
    /**
     * @OA\Get(
     *     path="/logs/{date}",
     *     summary="Retrieve logs by date",
     *     description="Fetches the log file for the specified date and returns its contents.",
     *     operationId="getLogsByDate",
     *     tags={"Logs"},
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         description="Date of the log file to retrieve (format: YYYY-MM-DD)",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logs retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Log")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Log file not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Log file not found"),
     *             @OA\Property(property="code", type="string", example="SERVER_ERROR"),
     *             @OA\Property(property="details", type="string", example="Log file not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error fetching logs",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error fetching logs"),
     *             @OA\Property(property="code", type="string", example="SERVER_ERROR"),
     *             @OA\Property(property="details", type="string", example="Exception message")
     *         )
     *     )
     * )
     */
    public function getLogsByDate($date)
    {
        try {
            $logPath = storage_path("logs/laravel-{$date}.log"); // Example: laravel-2024-02-21.log

            if (!file_exists($logPath)) {
                return errorResponse("Log file not found", "SERVER_ERROR", "Log file not found", 404);
            }

            $logs = file_get_contents($logPath);

            return successResponse("Logs retrieved successfully", explode("\n", $logs));
        } catch (\Exception $e) {
            return errorResponse("Error fetching logs", "SERVER_ERROR", $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/logs/clear",
     *     summary="Clear all log files",
     *     description="Deletes all log files from the storage/logs directory.",
     *     tags={"Logs"},
     *     @OA\Response(
     *         response=200,
     *         description="All logs cleared successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="All logs cleared successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error clearing all logs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Error clearing all logs"),
     *             @OA\Property(property="code", type="string", example="SERVER_ERROR"),
     *             @OA\Property(property="details", type="string", example="Detailed error message"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function clearAllLogs()
    {
        try {
            $logPath = storage_path('logs');
            $files = File::files($logPath);

            foreach ($files as $file) {
                File::delete($file);
            }

            return successResponse("All logs cleared successfully");
        } catch (\Exception $e) {
            return errorResponse("Error clearing all logs", "SERVER_ERROR", $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/logs/clear/{date}",
     *     summary="Clear logs by date",
     *     description="Deletes log files that contain the specified date in their filename.",
     *     operationId="clearLogsByDate",
     *     tags={"Logs"},
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         description="Date to filter log files by",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2023-10-01"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logs for the specified date cleared successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logs for 2023-10-01 cleared successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No logs found for the specified date",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No logs found for 2023-10-01"),
     *             @OA\Property(property="code", type="string", example="SERVER_ERROR"),
     *             @OA\Property(property="message", type="string", example="Not Found!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error clearing log by date",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error clearing log by date"),
     *             @OA\Property(property="code", type="string", example="SERVER_ERROR"),
     *             @OA\Property(property="message", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function clearLogsByDate($date)
    {
        try {
            $logPath = storage_path('logs');
            $files = File::files($logPath);
            $deleted = false;

            foreach ($files as $file) {
                if (strpos($file->getFilename(), $date) !== false) {
                    File::delete($file);
                    $deleted = true;
                }
            }

            if ($deleted) {
                return successResponse("Logs for $date cleared successfully");
            } else {
                return errorResponse("No logs found for $date", "SERVER_ERROR", "Not Found!", 404);
            }
        } catch (\Exception $e) {
            return errorResponse("Error clearing log by date", "SERVER_ERROR", $e->getMessage(), 500);
        }
    }
}
