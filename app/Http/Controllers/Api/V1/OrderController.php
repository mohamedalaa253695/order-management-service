<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\OrderCannotBeDeletedException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListOrderRequest;
use App\Http\Requests\Api\V1\Order\CreateOrderRequest;
use App\Http\Requests\Api\V1\Order\UpdateOrderRequest;
use App\Http\Resources\Api\V1\OrderCollection;
use App\Http\Resources\Api\V1\OrderResource;
use App\Interfaces\Api\V1\OrderServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderController extends Controller
{
    protected OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(CreateOrderRequest $request): JsonResource
    {
        $order = $this->orderService->createOrder($request->validated());
        return ResponseHelper::returnCreatedResource(new OrderResource($order), 'Order created successfully');
    }

    public function update(UpdateOrderRequest $request, $order_id): JsonResource
    {
        $order = $this->orderService->updateOrder($order_id, $request->validated());
        return ResponseHelper::returnResource(new OrderResource($order), 'Order updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->orderService->deleteOrder($id);
        return ResponseHelper::returnSuccessMessage('Order deleted successfully');
    }

    public function index(ListOrderRequest $request): JsonResponse
    {
        $orders = $this->orderService->getAllOrders($request);

        return ResponseHelper::returnCollection((new OrderCollection($orders))->toArray($request), 'Orders retrieved successfully');
    }

    public function show($id): JsonResource
    {
        $order = $this->orderService->getOrderById($id);
        return ResponseHelper::returnResource(new OrderResource($order), 'Order retrieved successfully');
    }
}
