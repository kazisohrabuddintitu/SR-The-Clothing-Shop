<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'category' => ['nullable', 'string', 'max:255'],
            'sizes' => ['nullable', 'array'],
            'sizes.*.size' => ['required_with:sizes', 'string', 'max:10'],
            'sizes.*.stock' => ['required_with:sizes', 'integer', 'min:0'],
        ];
    }
}
