<?php

namespace Tests\Feature;

use App\Exceptions\OrderCannotBeDeletedException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected OrderItem $orderItem;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
        $this->orderItem = OrderItem::factory()->create();
        $this->product = Product::factory()->create();

    }

    #[Test] public function it_can_create_an_order()
    {
        $data = [
            'status' => 'pending',
            'order_items' => [
                ['product_id' => $this->product->id, 'quantity' => 2, 'price' => 100],
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Order created successfully']);
    }

    #[Test] public function it_fails_to_create_an_order_with_invalid_data()
    {
        $data = [
            'status' => 'invalid_status',
            'order_items' => [
                ['product_id' => $this->product->id, 'quantity' => 2, 'price' => 100],
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $data);

        $response->assertStatus(422);
    }

    #[Test] public function it_can_update_an_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'status' => 'confirmed',
            'order_items' => [
                ['product_id' => $this->product->id, 'quantity' => 3, 'price' => 150],
            ],
        ];

        $response = $this->putJson("/api/v1/orders/{$order->id}", $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Order updated successfully']);
    }

    #[Test] public function it_fails_to_update_an_order_with_invalid_data()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'status' => 'invalid_status',
            'order_items' => [
                ['product_id' => $this->product->id, 'quantity' => 3, 'price' => 150],
            ],
        ];

        $response = $this->putJson("/api/v1/orders/{$order->id}", $data);

        $response->assertStatus(422);
    }

    #[Test] public function it_can_delete_an_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Order deleted successfully']);
    }


    #[Test] public function it_fails_to_delete_an_order_with_payments()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        $order->payments()->create(['payment_status' => 'successful', 'payment_method' => 'paypal']);

        $response = $this->deleteJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(400)
            ->assertJson(['message' => 'Order cannot be deleted because it has associated payments']);
    }


    #[Test] public function it_can_list_orders()
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['data' => [['id', 'status', 'total']]]]);
    }

    #[Test] public function it_can_show_an_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $order->id]]);
    }
}
