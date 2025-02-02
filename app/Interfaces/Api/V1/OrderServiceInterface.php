<?php

namespace App\Interfaces\Api\V1;

use App\Http\Requests\Api\V1\ListOrderRequest;

interface OrderServiceInterface
{
    public function createOrder(array $data);
    public function updateOrder($id, array $data);
    public function deleteOrder($id);
    public function getAllOrders(ListOrderRequest $request);
    public function getOrderById($id);
}
