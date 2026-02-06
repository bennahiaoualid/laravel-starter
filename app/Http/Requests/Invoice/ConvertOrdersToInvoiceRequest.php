<?php

namespace App\Http\Requests\Invoice;

class ConvertOrdersToInvoiceRequest extends StoreInvoiceRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // Add purchase_order_ids validation
        $rules['purchase_order_ids'] = 'required|string';

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Explode comma-separated IDs to array
        if ($this->has('purchase_order_ids')) {
            $ids = explode(',', $this->input('purchase_order_ids'));
            $ids = array_filter(array_map('trim', $ids));
            $ids = array_filter($ids, fn ($id) => ! empty($id) && is_numeric($id));
            $ids = array_map('intval', $ids);

            $this->merge([
                'purchase_order_ids_array' => array_values($ids),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $idsArray = $this->input('purchase_order_ids_array', []);
            if (empty($idsArray)) {
                $validator->errors()->add('purchase_order_ids', __('messages.validation.error.purchase_order_ids_required'));
            }
        });
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'purchase_order_ids.required' => __('messages.validation.error.purchase_order_ids_required'),
        ]);
    }

    /**
     * Get validated data with purchase_order_ids_array.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Ensure purchase_order_ids_array is included
        if ($key === null) {
            $validated['purchase_order_ids_array'] = $this->input('purchase_order_ids_array', []);
        }

        return $validated;
    }
}
