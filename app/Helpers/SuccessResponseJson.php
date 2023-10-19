<?php
namespace App\Helpers;

class SuccessResponseJson {
    public static function successResponse($data, int $code) {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $code);
    }
}
