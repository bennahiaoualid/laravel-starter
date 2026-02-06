<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $errorBag = 'createInvoice';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'client_id' => 'required|exists:parties,id',
            'supplier_id' => 'required|exists:parties,id',
            'paid' => 'nullable|numeric|min:0',
            'payment_type' => ['required', Rule::in(['cash', 'check', 'on_term', 'bank'])],
            'tva' => 'required|integer|min:1|max:100',
            'invoice_num' => 'required|string|max:255',
            'invoice_date' => 'required|date',
        ];

        $paymentType = $this->input('payment_type');

        // Conditional validation based on payment_type
        if ($paymentType === 'bank') {
            $rules['supplier_bank_id'] = 'required|exists:banks,id';
            $rules['supplier_account'] = 'required|string';
            $rules['client_bank_id'] = 'required|exists:banks,id';
            $rules['client_account'] = 'required|string';
        } elseif (in_array($paymentType, ['check', 'on_term'])) {
            $rules['client_bank_id'] = 'required|exists:banks,id';
            $rules['client_check_number'] = 'required|string';
        } else {
            // cash - all bank fields nullable
            $rules['supplier_bank_id'] = 'nullable';
            $rules['supplier_account'] = 'nullable';
            $rules['client_bank_id'] = 'nullable';
            $rules['client_account'] = 'nullable';
            $rules['client_check_number'] = 'nullable';
        }

        return $rules;
    }
}
