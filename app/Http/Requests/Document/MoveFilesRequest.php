<?php

namespace App\Http\Requests\Document;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MoveFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $errorBag = 'moveFiles';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'string'],
            'box_id' => ['required', 'integer', 'exists:boxes,id'],
            'move_reason' => ['required', 'string', 'min:3', 'max:255'],
            'delete_document' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'delete_document' => $this->has('delete_document') ? true : false,
        ]);
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $messages = session('messages', []);

        foreach ($validator->errors()->all() as $error) {
            $messages[] = [
                'message' => $error,
                'type' => 'error',
            ];
        }

        session()->flash('messages', $messages);

        parent::failedValidation($validator);
    }
}
