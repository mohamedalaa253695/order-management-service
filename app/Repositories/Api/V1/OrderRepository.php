<?php

namespace App\Repositories\Api\V1;

use App\Models\Order;

class OrderRepository
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function updateOrder($id, array $data): Order
    {
        $order = Order::findOrFail($id);
        $order->update($data);
        return $order;
    }

    public function deleteOrder($id): void
    {
        $order = Order::findOrFail($id);
        $order->delete();
    }

    public function getAllOrders($status = null, $perPage = 15)
    {
        return Order::status($status)->paginate($perPage);
    }

    public function getOrderById($id): Order
    {
        return Order::findOrFail($id);
    }
}
