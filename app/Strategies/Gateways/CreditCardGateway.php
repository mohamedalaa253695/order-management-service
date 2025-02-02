<?php

namespace App\Strategies\Gateways;


class CreditCardGateway
{
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->apiKey = env('CREDIT_CARD_API_KEY');
        $this->secretKey = env('CREDIT_CARD_SECRET_KEY');
    }

    public function process($data)
    {
        // Use $this->apiKey and $this->secretKey for processing
        return ['status' => 'successful'];
    }
}
