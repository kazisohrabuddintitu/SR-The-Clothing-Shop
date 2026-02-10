<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'size' => [
                'required',
                'string',
                'max:10',
                Rule::exists('product_sizes', 'size')->where('product_id', $this->input('product_id')),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
