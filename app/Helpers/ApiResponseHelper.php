<?php

if (!function_exists('successResponse')) {
    function successResponse($message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $status);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse($message, $errorCode = 'ERROR', $errors = [], $status = 400)
    {
        return response()->json([
            'success'   => false,
            'message'   => $message,
            'errorCode' => $errorCode,
            'errors'    => $errors
        ], $status);
    }
}
