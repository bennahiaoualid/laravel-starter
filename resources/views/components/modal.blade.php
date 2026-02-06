@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        'full' => 'sm:max-w-6xl',
    ][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        modalName: @js($name),
        modalId: @js($id),
        inputValue: @js($inputValue ?? ''),
        payload: {},
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
            return [...$el.querySelectorAll(selector)].filter(el => !el.hasAttribute('disabled'));
        },
        firstFocusable() { return this.focusables()[0]; },
        lastFocusable() { return this.focusables().slice(-1)[0]; },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable(); },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable(); },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1); },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement) - 1); },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="
        if (modalName === $event.detail.detail) {
            show = true;
            inputValue = $event.detail.value;
            payload = $event.detail.input_detail;
        }
    "
    x-on:close-modal.window="$event.detail.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0 w-screen"
    style="display: {{ $show ? 'flex' : 'none' }};"
>

    <!-- Backdrop -->
    <div
        class="fixed inset-0 bg-gray-500/75 transition-opacity"
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click="show = false"
    ></div>

    <!-- Modal Container -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        {{ $attributes->merge(['class' => 'relative z-50  max-h-[90vh] overflow-y-auto bg-white rounded-lg border border-gray-300 shadow-xl w-full p-4 '.$maxWidth]) }}
    >
        <!-- Header -->
        <h1 class="text-gray-800 {{ $title_size }} font-bold text-center mb-4 capitalize">
            {{ $modalhead }}
        </h1>

        <!-- Body -->
        <div class="p-2">
            {{ $slot }}
        </div>

        <!-- Footer -->
        @isset($modalfooter)
            <hr class="h-px my-2 bg-gray-300 border-0">
            <footer class="px-3 pb-3">
                {{ $modalfooter }}
            </footer>
        @endisset
    </div>
</div>
