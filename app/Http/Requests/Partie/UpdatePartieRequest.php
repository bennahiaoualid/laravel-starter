<?php

namespace App\Http\Requests\Partie;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update partie');
    }

    protected $errorBag = 'updatePartie';

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
            'name.fr' => 'required|string|min:3|max:255',
            'nif' => 'nullable|numeric',
            'nis' => 'nullable|numeric',
            'art' => 'nullable|numeric',
            'rc' => 'nullable|array',
            'rc.ar' => 'nullable|string|max:255',
            'rc.en' => 'nullable|string|max:255',
            'rc.fr' => 'nullable|string|max:255',
            'mf' => 'nullable|numeric',
            'field_id' => 'required|exists:fields,id',
            'address' => 'nullable|array',
            'address.ar' => 'nullable|string|max:255',
            'address.en' => 'nullable|string|max:255',
            'address.fr' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_account' => 'nullable|string|max:255',
            'initial_debt' => 'required|numeric',
            'logo' => 'nullable|image|mimes:png,jpeg,jpg|max:2048',
            'is_my_company' => 'nullable|boolean',
        ];
    }
}
