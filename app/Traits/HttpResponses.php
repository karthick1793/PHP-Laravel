<?php

namespace App\Traits;

use App\Exceptions\ApiResponseException;

trait HttpResponses
{
    public function success($message, $data = [], $code = 200, $customCode = null)
    {
        return $this->httpResponse($message, $data, $code, $customCode);
    }

    public function error($message, $data = [], $code = 400, $customCode = null)
    {
        return $this->httpResponse($message, $data, $code, $customCode);
    }

    public function throwException($message, $code = 400)
    {
        throw new ApiResponseException($message, $code);
    }

    public function httpResponse($message, $data, $code, $customCode)
    {
        return response()->json([
            'status_code' => $customCode ?? $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
