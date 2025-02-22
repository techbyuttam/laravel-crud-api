<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function logError(\Throwable $e, $context = [])
    {
        Log::error(json_encode([
            'timestamp'   => now()->toDateTimeString(),
            'level'       => 'ERROR',
            'api'         => request()->fullUrl(),
            'method'      => request()->method(),
            'controller'  => $context['controller'] ?? 'Unknown',
            'function'    => $context['function'] ?? 'Unknown',
            'message'     => $e->getMessage(),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            // 'stack_trace' => $e->getTraceAsString(),
            'user_id'     => optional(Auth::user())->id ?? 'Guest',
        ], JSON_PRETTY_PRINT));
    }
}
