<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'image_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'category' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sizes' => ['sometimes', 'array'],
            'sizes.*.size' => ['required_with:sizes', 'string', 'max:10'],
            'sizes.*.stock' => ['required_with:sizes', 'integer', 'min:0'],
        ];
    }
}
