<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update product');
    }

    protected $errorBag = 'updateProduct';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.ar' => 'nullable|string|max:255',
            'name.en' => 'nullable|string|max:255',
            'name.fr' => 'required|string|min:1|max:255',
            'type_id' => 'nullable|exists:fields,id',
            'unit_id' => 'required|exists:product_units,id',
            'unit_price' => 'required|numeric|min:0',
        ];
    }
}
