<?php

namespace App\Services\Api\V1;

use App\Exceptions\OrderCannotBeDeletedException;
use App\Exceptions\OrderNotFoundException;
use App\Http\Requests\Api\V1\ListOrderRequest;
use App\Interfaces\Api\V1\OrderServiceInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Api\V1\OrderRepository;
use http\Exception\InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nette\Schema\ValidationException;

class OrderService implements OrderServiceInterface
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(array $data): Order
    {
//        dd('fofo');
        DB::beginTransaction();

        try {

            if (empty($data['order_items']) || !is_array($data['order_items'])) {
                throw new InvalidArgumentException('Order items are required and must be an array.');
            }

            $orderItems = $data['order_items'];
            unset($data['order_items']);
            $data['user_id'] = auth()->user()->id;

            // Calculate total and prepare items for bulk insertion
            $data['total'] = 0;
            $items = [];

            foreach ($orderItems as $item) {
                if (!isset($item['product_id'], $item['price'], $item['quantity'])) {
                    throw new InvalidArgumentException('Order items must have product_id, price, and quantity.');
                }

                $data['total'] += $item['price'] * $item['quantity'];
                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];
            }

            // Create the order
            $order = $this->orderRepository->createOrder($data);

            // Bulk insert order items
            foreach ($items as &$item) {
                $item['order_id'] = $order->id;
            }

            OrderItem::insert($items);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            throw $e;
        }
    }


    public function updateOrder($id, array $data): Order
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->getOrderById($id);

            if (isset($data['order_items'])) {
                $orderItems = $data['order_items'];
                unset($data['order_items']);

                // Calculate total and prepare items for bulk insertion
                $data['total'] = 0;
                $items = [];

                foreach ($orderItems as $item) {
                    if (!isset($item['product_id'], $item['price'], $item['quantity'])) {
                        throw new \InvalidArgumentException('Order items must have product_id, price, and quantity.');
                    }

                    $data['total'] += $item['price'] * $item['quantity'];
                    $items[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ];
                }

                $order->orderItems()->delete();

                foreach ($items as &$item) {
                    $item['order_id'] = $order->id;
                }

                OrderItem::insert($items);
            }

            $order = $this->orderRepository->updateOrder($id, $data);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
//            dd($e->getMessage());
            DB::rollBack();
            Log::error('Error updating order: ' . $e->getMessage());
            throw  new OrderNotFoundException($e);
        }
    }

    /**
     * @throws \Exception
     */
    public function deleteOrder($id)
    {
        $order = $this->orderRepository->getOrderById($id);

        if ($order->payments()->exists()) {
            throw new OrderCannotBeDeletedException("Order cannot be deleted because it has associated payments", 400);
        }

        $this->orderRepository->deleteOrder($id);
    }

    public function getAllOrders(ListOrderRequest $request)
    {
        return $this->orderRepository->getAllOrders($request->status, $request->per_page);
    }

    public function getOrderById($id)
    {
        return $this->orderRepository->getOrderById($id);
    }
}
