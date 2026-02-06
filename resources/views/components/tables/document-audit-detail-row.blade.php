<div class="p-4 bg-gray-50 border-t border-b border-gray-400">
    @if($row->old_box && $row->action === 'move')
        <div class="mb-4">
            <div class="bg-white p-3 rounded-lg shadow-sm">
                <dt class="text-sm text-gray-600 mb-1">
                    {{ __('messages.global.old_box') }}
                </dt>
                <dd class="text-sm text-gray-900">{{ $row->old_box }}</dd>
            </div>
        </div>
    @endif

    @if($row->files && count($row->files) > 0)
        <div class="mb-4">
            <h4 class="font-semibold text-gray-700 mb-3">{{ __('document.info.files') }}</h4>
            <div class="space-y-2">
                @foreach($row->files as $fileName => $description)
                    <div class="bg-white p-2 rounded-lg shadow-sm">
                        <p class="text-sm font-medium text-gray-900">{{ $fileName }}</p>
                        @if($description)
                            <p class="text-sm text-gray-600 mt-1">{{ $description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($row->description && count($row->description) > 0)
        <div>
            <h4 class="font-semibold text-gray-700 mb-3">{{ __('messages.details') }}</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($row->description as $key => $value)
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <dt class="text-sm text-gray-600 mb-1">
                            {{ __('document.desecrption_keys_valus.' . $key, [], null, $key) }}
                        </dt>
                        <dd class="text-sm text-gray-900">{{ $value }}</dd>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

