<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'status' => 'nullable|in:pending,confirmed,cancelled',
            'order_items' => 'nullable|array|min:1',
            'order_items.*.product_id' => 'nullable|exists:products,id',
            'order_items.*.quantity' => 'nullable|integer|min:1',
            'order_items.*.price' => 'nullable|numeric|min:0.01',
        ];
    }

    /**
     * Get the validation data that will be used by the validator instance.
     *
     * @return array
     */

}
