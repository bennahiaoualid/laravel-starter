<?php

namespace App\Http\Requests\MoneyTransaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoneyTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected $errorBag = 'createMoneyTransaction';

    public function rules(): array
    {
        return [
            'partable_id' => 'required|integer',
            'partable_type' => 'required|string|in:partie,investor',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:in,out',
            'is_debt' => 'required|boolean',
            'note' => 'nullable|string|max:1000',
            'transaction_date' => 'required|date_format:Y-m-d',
        ];
    }

    public function attributes(): array
    {
        return [
            'partable_id' => __('user.money_transaction.partable'),
            'partable_type' => __('user.money_transaction.partable_type'),
            'amount' => __('user.money_transaction.amount'),
            'type' => __('user.money_transaction.type'),
            'is_debt' => __('user.money_transaction.is_debt'),
            'note' => __('user.money_transaction.note'),
            'transaction_date' => __('user.money_transaction.date'),
        ];
    }
}
