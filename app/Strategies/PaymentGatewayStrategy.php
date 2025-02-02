<?php

namespace App\Strategies;

use App\Strategies\Gateways\CreditCardGateway;
use App\Strategies\Gateways\PaypalGateway;
use App\Strategies\Gateways\BankTransferGateway;

class PaymentGatewayStrategy
{
    protected array $gateways = [
        'credit_card' => CreditCardGateway::class,
        'paypal' => PaypalGateway::class,
        'bank_transfer' => BankTransferGateway::class,
    ];

    /**
     * @throws \Exception
     */
    public function getGateway($method)
    {
        if (!isset($this->gateways[$method])) {
            throw new \Exception('Payment method not supported');
        }
        return new $this->gateways[$method];
    }
}
