<?php

namespace App\Repositories\Api\V1;

use App\Models\Payment;
use App\Models\Order;

class PaymentRepository
{
    public function createPayment(array $data, $paymentResult)
    {
        return Payment::create([
            'order_id' => $data['order_id'],
            'payment_status' => $paymentResult['status'],
            'payment_method' => $data['payment_method'],
        ]);
    }

    public function getAllPayments()
    {
        return Payment::all();
    }

    public function getPaymentById($id)
    {
        return Payment::findOrFail($id);
    }

    public function getOrderById($id)
    {
        return Order::findOrFail($id);
    }
}
