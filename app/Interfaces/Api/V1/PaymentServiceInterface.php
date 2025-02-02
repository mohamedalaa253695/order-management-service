<?php

namespace App\Interfaces\Api\V1;

interface PaymentServiceInterface
{
    public function processPayment(array $data);
    public function getAllPayments();
    public function getPaymentById($id);
}
