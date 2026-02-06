@php
    $classes = ($icon ?? false)
                    ? 'ps-16'
                    : 'ps-4';
@endphp
<div class="relative mb-2 mt-2 text-base">
    @isset($input_icon)
        <div class="absolute text-gray-600 flex items-center px-4 border-e border-gray-400 h-full">
                {{ $input_icon }}
        </div>
    @endisset
    <input
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'text-base text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center border border-gray-400 rounded-sm ' . $classes
                                    ,'placeholder'=>$placeholder]) !!}
    />
</div>
