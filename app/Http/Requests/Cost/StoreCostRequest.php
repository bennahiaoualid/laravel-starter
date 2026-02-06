<?php

namespace App\Http\Requests\Cost;

use Illuminate\Foundation\Http\FormRequest;

class StoreCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected $errorBag = 'createCost';

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0',
            'responsible' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
            'date' => 'required|date_format:Y-m-d',
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => __('user.cost.amount'),
            'responsible' => __('user.cost.responsible'),
            'note' => __('user.cost.note'),
            'date' => __('user.cost.date'),
        ];
    }
}
