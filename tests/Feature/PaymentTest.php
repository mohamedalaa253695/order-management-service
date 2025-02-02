<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
        $this->order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'confirmed']);
    }

    #[Test] public function it_can_create_a_payment()
    {
        $data = [
            'order_id' => $this->order->id,
            'payment_method' => 'paypal',
        ];

        $response = $this->postJson('/api/v1/payments', $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Payment processed successfully']);
    }

    #[Test] public function it_fails_to_create_a_payment_for_invalid_order_status()
    {
        $this->order->update(['status' => 'pending']);
        $data = [
            'order_id' => $this->order->id,
            'payment_method' => 'paypal',
        ];

        $response = $this->postJson('/api/v1/payments', $data);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Payments can only be processed for orders in the confirmed status.']);
    }

    #[Test] public function it_fails_to_create_a_payment_with_invalid_data()
    {
        $data = [
            'order_id' => null,
            'payment_method' => '',
        ];

        $response = $this->postJson('/api/v1/payments', $data);

        $response->assertStatus(422);
    }

    #[Test] public function it_can_list_payments()
    {
        Payment::factory()->count(3)->create(['order_id' => $this->order->id]);

        $response = $this->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'order_id', 'payment_status', 'payment_method']]]);
    }

    #[Test] public function it_can_show_a_payment()
    {
        $payment = Payment::factory()->create(['order_id' => $this->order->id]);

        $response = $this->getJson("/api/v1/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $payment->id]]);
    }

    #[Test] public function it_fails_to_show_a_non_existent_payment()
    {
        $response = $this->getJson('/api/v1/payments/999');

        $response->assertStatus(404);
    }
}
