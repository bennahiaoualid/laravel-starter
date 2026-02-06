<?php

namespace App\Http\Requests\InvoiceTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceTemplateRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:invoice_templates,name',
            'description' => 'nullable|string',
            'template_config' => 'required|array',
        ];
    }
}
