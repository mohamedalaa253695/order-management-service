<?php

namespace App\Helpers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Validator;

class ResponseHelper
{

    public static function returnError(string $msg, int $code = 422)
    {
        return response()->json([
            'code' => $code,
            'message' => $msg,
            'success' => false,
            'status' => $code,
        ])->setStatusCode($code);
    }

    public static function returnSuccessMessage(string $msg = "", int $code = 200)
    {
        return response()->json([
            'code' => $code,
            'message' => $msg,
            'success' => true,
            'status' => $code,
        ])->setStatusCode($code);
    }

    public static function returnResource(JsonResource $jsonResource, string $msg = "", int $code = 200)
    {
        return $jsonResource->additional([
            'code' => $code,
            'message' => $msg,
            'success' => true,
            'status' => $code,
        ]);
    }

    public static function returnCreatedResource(JsonResource $jsonResource, string $msg = "", int $code = 201)
    {
        return $jsonResource->additional([
            'code' => $code,
            'message' => $msg,
            'success' => true,
            'status' => $code,
        ]);
    }

    public static function returnData(array $dataArr = [], string $msg = "", int $code = 200)
    {
        return response()->json([
            'data' => $dataArr,
            'code' => $code,
            'message' => $msg,
            'success' => true,
            'status' => $code,
        ]);
    }

    public static function returnCollection(array $dataArr = [], string $msg = "", int $code = 200)
    {
        return response()->json([
            'data' => $dataArr,
            'code' => $code,
            'message' => $msg,
            'success' => true,
            'status' => $code,
        ]);
    }

    public static function returnValidationError(Validator $validator, string $msg = '', int $code = 422)
    {
        return response()->json([
            'errors' => $validator->errors(),
            'code' => $code,
            'message' => $validator->errors()->first(),
            'success' => false,
            'status' => $code,
        ]);
    }

    public static function returnArrayErrors(array $errors, string $msg, int $code = 422)
    {
        return response()->json([
            'errors' => $errors,
            'code' => $code,
            'message' => $msg,
            'success' => false,
            'status' => $code,
        ]);
    }

    public static function returnObject($dataObj, string $msg = "", int $code = 200)
    {
        return response()->json([
            'data' => $dataObj,
            'code' => $code,
            'message' => $msg,
            'success' => true,
            'status' => $code,
        ]);
    }
}
