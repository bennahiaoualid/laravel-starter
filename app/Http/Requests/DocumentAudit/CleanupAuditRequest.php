<?php

namespace App\Http\Requests\DocumentAudit;

use Illuminate\Foundation\Http\FormRequest;

class CleanupAuditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $errorBag = 'cleanupAudit';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_date' => ['required', 'date', 'date_format:Y-m-d'],
            'to_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'cleanup_deleted_documents' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cleanup_deleted_documents' => $this->has('cleanup_deleted_documents') ? (bool) $this->cleanup_deleted_documents : false,
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'from_date' => __('document_audit.cleanup.from_date'),
            'to_date' => __('document_audit.cleanup.to_date'),
            'cleanup_deleted_documents' => __('document_audit.cleanup.cleanup_deleted_documents'),
        ];
    }
}
