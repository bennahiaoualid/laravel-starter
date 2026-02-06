@props(['disabled' => false, 'placeholder' => null, 'options' => [], 'name' => null, 'value' => null])

@php
    $isLivewire = isset($__livewire);
    $initialSelected = old($name ?? '', $value);
@endphp

<div 
    x-data="{
        open: false,
        search: '',
        selected: @if($isLivewire)
            @entangle($attributes->wire('model')).live
        @else
            @js($initialSelected)
        @endif,
        options: @js($options),
        dropdownPosition: 'bottom',
        isSelecting: false, // Flag to prevent clearing on select
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase()));
        },
        select(option) {
            this.isSelecting = true; // Set flag before selecting
            this.selected = option.value;
            this.open = false;
            this.search = option.text;
            this.$nextTick(() => {
                this.isSelecting = false; // Reset flag after selection
                // Dispatch event with name and value
                @if($name)
                window.dispatchEvent(new CustomEvent('searchable-select-change', {
                    detail: {
                        name: '{{ $name }}',
                        value: option.value
                    }
                }));
                @endif
            });
        },
        checkPosition() {
            if (!this.open) return;
            const rect = $refs.container.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            const dropdownHeight = 240;
            const spaceBelow = viewportHeight - rect.bottom;
            const spaceAbove = rect.top;
            
            if (spaceBelow >= dropdownHeight || spaceBelow > spaceAbove) {
                this.dropdownPosition = 'bottom';
            } else {
                this.dropdownPosition = 'top';
            }
        },
        init() {
            if (this.selected) {
                const found = this.options.find(o => o.value == this.selected);
                if (found) this.search = found.text;
            }
            
            // Listen for update-searchable-select event
            @if($name)
            const componentName = '{{ $name }}';
            window.addEventListener('update-searchable-select', (e) => {
                if (e.detail?.target === componentName) {
                    this.selected = e.detail.value;
                    const found = this.options.find(o => o.value == e.detail.value);
                    if (found) this.search = found.text;
                }
            });
            @endif
            
            // Watch for external changes (from Livewire)
            $watch('selected', (value) => {
                if (!this.isSelecting) {
                    const found = this.options.find(o => o.value == value);
                    if (found) {
                        this.search = found.text;
                    } else if (!value) {
                        this.search = '';
                    }
                }
            });
            
            // Watch for search changes - clear selection if search doesn't match
            $watch('search', (value) => {
                if (!this.isSelecting) {
                    const found = this.options.find(o => o.text.toLowerCase() === value.toLowerCase());
                    if (!found && value !== '') {
                        // Search text doesn't match any option, clear selection
                        this.selected = null;
                        // Dispatch event with null value
                        @if($name)
                        window.dispatchEvent(new CustomEvent('searchable-select-change', {
                            detail: {
                                name: '{{ $name }}',
                                value: null
                            }
                        }));
                        @endif
                    } else if (value === '') {
                        // Search is empty, clear selection
                        this.selected = null;
                        // Dispatch event with null value
                        @if($name)
                        window.dispatchEvent(new CustomEvent('searchable-select-change', {
                            detail: {
                                name: '{{ $name }}',
                                value: null
                            }
                        }));
                        @endif
                    }
                }
            });
        }
    }" 
    x-init="init()" 
    class="relative mb-2 mt-2"
>
    @unless($isLivewire)
        <input 
            type="hidden"
            x-model="selected"
            {{-- only print Blade name if user didn't pass any Alpine :name/x-bind:name --}}
            @if($name && !$attributes->has(':name') && !$attributes->has('x-bind:name'))
                name="{{ $name }}"
            @else
            {{ $attributes->merge() }}
            @endif
        />
    @endunless

    <input
        x-ref="container"
        type="text"
        x-model="search"
        @focus="open = true; $nextTick(() => checkPosition())"
        @click="open = true; $nextTick(() => checkPosition())"
        @keydown.arrow-down.prevent="open = true; $nextTick(() => { checkPosition(); $refs.listbox?.focus(); })"
        @keydown.escape="open = false"
        :placeholder="'{{ $placeholder ?? __('messages.global.choose') }}'"
        class="text-gray-600 border focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center text-sm border-gray-400 rounded-sm px-3"
        autocomplete="off"
        {{ $disabled ? 'disabled' : '' }}
    >
    
    {{-- Clear button --}}
    <button 
        type="button"
        x-show="search !== ''"
        @click="search = ''; selected = null; open = false;"
        class="absolute end-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
        tabindex="-1"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        :class="{
            'absolute z-10 w-full bg-white border border-gray-300 rounded-sm shadow-lg max-h-60 overflow-auto': true,
            'mt-1': dropdownPosition === 'bottom',
            'mb-1 bottom-full': dropdownPosition === 'top'
        }"
        x-cloak
    >
        <ul tabindex="-1" x-ref="listbox">
            <template x-if="filteredOptions.length === 0">
                <li class="px-4 py-2 text-gray-400">No results found</li>
            </template>
            <template x-for="option in filteredOptions" :key="option.value">
                <li
                    @click="select(option)"
                    :class="{
                        'bg-indigo-100 text-indigo-700': selected == option.value,
                        'hover:bg-gray-100': selected != option.value,
                        'cursor-pointer px-4 py-2': true
                    }"
                    x-text="option.text"
                ></li>
            </template>
        </ul>
    </div>
</div>