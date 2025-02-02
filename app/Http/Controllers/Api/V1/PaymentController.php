<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Payment\CreatePaymentRequest;
use App\Http\Resources\Api\V1\PaymentResource;
use App\Interfaces\Api\V1\PaymentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentController extends Controller
{
    protected PaymentServiceInterface $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(CreatePaymentRequest $request): JsonResource
    {
        $payment = $this->paymentService->processPayment($request->validated());
        return ResponseHelper::returnCreatedResource(new PaymentResource($payment), 'Payment processed successfully');
    }

    public function index(): JsonResponse
    {
        $payments = $this->paymentService->getAllPayments();
        return ResponseHelper::returnData(PaymentResource::collection($payments)->toArray(request()), 'Payments retrieved successfully');
    }

    public function show($id): JsonResource
    {
        $payment = $this->paymentService->getPaymentById($id);
        return ResponseHelper::returnResource(new PaymentResource($payment), 'Payment retrieved successfully');
    }
}
