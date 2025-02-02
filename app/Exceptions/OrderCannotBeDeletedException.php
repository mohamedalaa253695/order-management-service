<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderCannotBeDeletedException extends Exception
{
    protected $statusCode;

    public function __construct($message = "Order cannot be deleted", $statusCode = 400)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // You can log the exception or perform other reporting actions here
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'service' => 'Order Service',
            'message' => $this->getMessage(),
            'code' => $this->statusCode,
            'status' => $this->statusCode,
            'success' => false,
        ], $this->statusCode);
    }
}
