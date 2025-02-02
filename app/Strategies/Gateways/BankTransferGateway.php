<?php

namespace App\Strategies\Gateways;


class BankTransferGateway
{
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->apiKey = env('BANK_TRANSFER_API_KEY');
        $this->secretKey = env('BANK_TRANSFER_SECRET_KEY');
    }

    public function process($data)
    {
        // Use $this->apiKey and $this->secretKey for processing
        return ['status' => 'successful'];
    }
}
