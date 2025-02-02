<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|required|in:pending,confirmed,cancelled,paid',
            'order_items' => 'sometimes|required|array|min:1',
            'order_items.*.product_id' => 'required_with:order_items|exists:products,id',
            'order_items.*.quantity' => 'required_with:order_items|integer|min:1',
            'order_items.*.price' => 'required_with:order_items|numeric|min:0.01',
        ];
    }
}
