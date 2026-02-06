@can('update money_transaction')
<x-modal name="add-money-transaction" title="Add Money Transaction" max_width="full" :show="$errors->hasBag('createMoneyTransaction')"
    x-data="{
        partable_id: '{{ old('partable_id') }}',
        partable_type: '{{ old('partable_type') }}',
        name: '{{ old('name_transction') }}',
        setContext(id, type, name) {
            this.partable_id = id ?? this.partable_id;
            this.partable_type = type ?? this.partable_type;
            this.name = name ?? this.name;
        }
    }"
    x-on:open-modal.window="
        if ($event.detail?.detail === 'add-money-transaction') {
            const id = $event.detail?.value ?? null;
            const type = $event.detail?.input_detail?.partableType ?? null;
            const name = $event.detail?.input_detail?.name ?? null;
            setContext(id, type, name);
        }
    "
    
    
    
>
    <x-slot:modalhead>
        {{__("form.money_transaction.add")}} <span class="text-sm text-gray-500" x-text="name"></span>
    </x-slot>

    <form id="add-money-transaction-form" method="post" action="{{ route('partners.money-transactions.store') }}" class="space-y-2">
        @csrf
        @method('post')
        <input type="hidden" name="partable_id" x-model="partable_id">
        <input type="hidden" name="partable_type" x-model="partable_type">

        <input type="hidden" name="name_transction" x-model="name">


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <div>
                    @php
                        $typeOptions = [
                            ['value' => 'in', 'text' => __('user.money_transaction.types.in'), 'selected' => false],
                            ['value' => 'out', 'text' => __('user.money_transaction.types.out'), 'selected' => false],
                        ];
                    @endphp
                    <x-form.select-box-field 
                        id="type" 
                        name="type" 
                        :options="$typeOptions"
                        :label="ucwords(__('user.money_transaction.type'))"
                        for="type"
                        :error_messages="$errors->createMoneyTransaction->get('type')"
                    />
                </div>

                <div>
                    <x-form.field 
                        label="{{ ucwords(__('user.money_transaction.amount')) }}" 
                        for="amount" 
                        id="amount"
                        name="amount" 
                        type="number" 
                        step="0.01" 
                        value="1" 
                        min="1" 
                        class="mt-1 block w-full"
                        :error_messages="$errors->createMoneyTransaction->get('amount')"
                    />
                </div>

                <div>
                    @php
                        $isDebtOptions = [
                            ['value' => 0, 'text' => __('messages.global.no'), 'selected' => false],
                            ['value' => 1, 'text' => __('messages.global.yes'), 'selected' => false],
                        ];
                    @endphp
                    <x-form.select-box-field 
                        id="is_debt" 
                        name="is_debt" 
                        :options="$isDebtOptions"
                        :label="ucwords(__('user.money_transaction.is_debt'))"
                        for="is_debt"
                        :error_messages="$errors->createMoneyTransaction->get('is_debt')"
                    />
                </div>
            </div>

            <div>
                <div>
                    <x-form.field 
                        label="{{ ucwords(__('user.money_transaction.date')) }}" 
                        for="transaction_date" 
                        id="transaction_date"
                        name="transaction_date" 
                        type="text" 
                        class="mt-1 block w-full date-input"
                        :error_messages="$errors->createMoneyTransaction->get('transaction_date')"
                    />
                </div>

                <div>
                    <x-form.field 
                        label="{{ ucwords(__('user.money_transaction.note')) }}" 
                        for="note" 
                        id="note"
                        name="note" 
                        type="text" 
                        class="mt-1 block w-full"
                        :error_messages="$errors->createMoneyTransaction->get('note')"
                    />
                </div>
            </div>
        </div>
    </form>
    <x-slot:modalfooter>
        <div class="flex justify-end">
            <x-button form="add-money-transaction-form" color_type="success" >{{ __('form.actions.save') }}</x-button>
        </div>
    </x-slot>
</x-modal>
@endcan


