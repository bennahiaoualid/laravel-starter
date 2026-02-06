@if(isset($id))
    <div class="flex justify-between items-center my-2 p-4 shadow-sm">
        <h2 class="text-xl font-bold">{{__('links.money_transaction.list')}}</h2>
        @can("update money_transaction")
            <div x-data>
                <x-button color_type="success" size="sm"
                    x-on:click="
                        $dispatch('open-modal', 
                            { detail: 'add-money-transaction' , 
                            value:'{{$id}}',
                            input_detail: { partableType: '{{$type}}' , name: '{{ $name }}' }
                            })"
                    >
                    <x-slot:icon>
                        <i class="fa-solid fa-plus me-2"></i>
                    </x-slot:icon>
                    {{__("form.actions.add")}}
                </x-button>
            </div>
        @endcan
    </div>
@endif

<div class="overflow-x-auto max-w-[95vw] md:max-w-full pt-2 my-4">
@if(isset($id))
    <livewire:money-transaction-table :partableId="$id" :partableName="$name" :type="$type" />
@else
    <livewire:money-transaction-table />
@endif
</div>
<x-modal name="delete" title="My Modal" :show="false" :inputValue="old('id')">
    <x-slot:modalhead>
        {{__("form.money_transaction.delete")}}
    </x-slot>
    <form id="delete-form" method="post" action="{{route("partners.money-transactions.delete")}}" class="space-y-2">
        @csrf
        @method('post')

        <div>
            <input type="hidden" name="id" x-model="inputValue"/>
            <p class="my-1">
                {{__("form.actions.confirm_delete")}}
                <span class="text-danger" x-text="payload.transaction"></span>
            </p>
        </div>

    </form>
    <x-slot:modalfooter>
        <div class="flex justify-end">
            <x-button form="delete-form" color_type="danger" >{{ __('form.actions.delete') }}</x-button>
        </div>
    </x-slot>
</x-modal>