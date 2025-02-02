<?php

namespace App\Strategies\Gateways;


class PaypalGateway
{
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->apiKey = env('PAYPAL_API_KEY');
        $this->secretKey = env('PAYPAL_SECRET_KEY');
    }

    public function process($data)
    {
        // Use $this->apiKey and $this->secretKey for processing
        return ['status' => 'successful'];
    }
}
