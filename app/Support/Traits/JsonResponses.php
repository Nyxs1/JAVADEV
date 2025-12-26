<?php

namespace App\Support\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Standardized JSON Response Trait
 */
trait JsonResponses
{
    protected function jsonSuccess(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            ...$data,
        ], $status);
    }

    protected function jsonError(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    protected function jsonValidationError(string $message, array $errors = []): JsonResponse
    {
        return $this->jsonError($message, $errors, 422);
    }

    protected function jsonServerError(string $message = 'Server error occurred.'): JsonResponse
    {
        return $this->jsonError($message, [], 500);
    }

    protected function jsonTooManyRequests(string $message, int $retryAfter = 0): JsonResponse
    {
        $data = $retryAfter > 0 ? ['retry_after' => $retryAfter] : [];
        return response()->json([
            'success' => false,
            'message' => $message,
            ...$data,
        ], 429);
    }
}
