<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        $rules = [
            'title' => [($this->method() === 'POST' ? 'required' : 'nullable')],
            'category_id' => [($this->method() === 'POST' ? 'required' : 'nullable')],
            'image' => $this->imageRules(),
            'description' => [($this->method() === 'POST' ? 'required' : 'nullable')],
            'price' => [($this->method() === 'POST' ? 'required': 'nullable'), 'numeric'],
            'price_before_discount' => ['nullable', 'numeric'],
            'stock' => [($this->method() === 'POST' ? 'required' : 'nullable')]
        ];
        return $rules;
    }

    private function imageRules(): array
    {
        return [($this->method() === 'POST' ? 'required' : 'nullable'), 'image', 'mimes:png,jpg'];
    }
}
