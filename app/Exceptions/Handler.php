<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {


        if ($e instanceof ValidationException) {
            return response()->json([
                'service' => "Order Service",
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'code' => $e->getCode() ?: 422,
                'status' => $e->getCode() ?: 422,
                'success' => false,
            ], $e->getCode() ?: 422);
        }
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'service' => "Order Service",
                'message' => $e->getMessage(),
                'code' => $e->getCode() ?: 404,
                'status' => $e->getCode() ?: 404,
                'success' => false,
            ], $e->getCode() ?: 404);
        } else if ($e instanceof \Exception) {
            return response()->json([
                'service' => "Order Service",
                'message' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
                'status' => $e->getCode() ?: 500,
                'success' => false,
            ], $e->getCode() ?: 500);
        }

        return parent::render($request, $e);
    }

    final public function getExceptionValue(Throwable $e, $option, $fallback = null)
    {
        if (method_exists(get_class($e), $option)) {
            return $e->{$option}();
        } else {
            if (is_callable($fallback)) {
                return $fallback($e);
            }

            return $fallback;
        }
    }
}
