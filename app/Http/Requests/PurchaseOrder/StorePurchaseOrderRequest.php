<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $errorBag = 'createPurchaseOrder';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:parties,id',
            'supplier_id' => 'required|exists:parties,id',
            'order_num' => 'required|string|max:255',
            'order_date' => 'required|date',
            'paid' => 'nullable|numeric|min:0',
        ];
    }
}
