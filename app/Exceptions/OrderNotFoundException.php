<?php

namespace App\Exceptions;

use Exception;

class OrderNotFoundException extends Exception
{
    protected $message = 'Order not found';

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
