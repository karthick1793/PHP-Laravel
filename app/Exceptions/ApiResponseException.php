<?php

namespace App\Exceptions;

use Exception;

class ApiResponseException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status_code' => $this->getCode(),
        ], $this->getCode());
    }
}
