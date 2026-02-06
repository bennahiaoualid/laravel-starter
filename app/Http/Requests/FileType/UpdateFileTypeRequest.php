<?php

namespace App\Http\Requests\FileType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFileTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $errorBag = 'updateFileType';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fileTypeId = $this->route('fileType')->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('file_types', 'name')->ignore($fileTypeId)],
            'description' => 'nullable|string|max:1000',
        ];
    }
}
