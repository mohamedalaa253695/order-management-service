<?php

namespace App\Services\Api\V1;

use App\Interfaces\Api\V1\PaymentServiceInterface;
use App\Repositories\Api\V1\PaymentRepository;
use App\Strategies\PaymentGatewayStrategy;

class PaymentService implements PaymentServiceInterface
{
    protected PaymentRepository $paymentRepository;
    protected PaymentGatewayStrategy $paymentGatewayStrategy;

    public function __construct(PaymentRepository $paymentRepository, PaymentGatewayStrategy $paymentGatewayStrategy)
    {
        $this->paymentRepository = $paymentRepository;
        $this->paymentGatewayStrategy = $paymentGatewayStrategy;
    }

    public function processPayment(array $data)
    {
        $order = $this->paymentRepository->getOrderById($data['order_id']);
        if ($order->status !== 'confirmed') {
            throw new \Exception('Payments can only be processed for orders in the confirmed status.',400);
        }

        $paymentGateway = $this->paymentGatewayStrategy->getGateway($data['payment_method']);
        $paymentResult = $paymentGateway->process($data);

        $payment = $this->paymentRepository->createPayment($data, $paymentResult);

        if ($paymentResult['status'] === 'successful') {
            $order->update(['status' => 'paid']);
        }

        return $payment;
    }

    public function getAllPayments()
    {
        return $this->paymentRepository->getAllPayments();
    }

    public function getPaymentById($id)
    {
        return $this->paymentRepository->getPaymentById($id);
    }
}
