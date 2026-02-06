<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $errorBag = 'updateDocument';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_types' => ['required', 'array', 'min:1'],
            'file_types.*.type_id' => ['required', 'integer', 'exists:file_types,id'],
            'file_types.*.description' => ['nullable', 'string', 'max:255'],
            'submitter' => ['required', 'string', 'min:3', 'max:60'],
        ];
    }
}
