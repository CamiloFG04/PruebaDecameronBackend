<?php
namespace App\Helpers;

class ErrorResponseJson {
    public static function errorResponse(string $message, int $code) {
        return response()->json([
            'success' => false,
            'errors' => $message,
        ], $code);
    }
}
