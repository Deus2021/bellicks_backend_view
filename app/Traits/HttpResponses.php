<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HttpResponses
{

    protected function success($data, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Request was successful',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error($data, $message = null, $code): JsonResponse
    {
        return response()->json([
            'status' => 'Error has occurred...',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
