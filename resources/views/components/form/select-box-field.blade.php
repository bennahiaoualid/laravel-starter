@props([
    // Label props
    'label' => null,
    'for' => null,
    'render_label' => true,
    
    // Select box props
    'name' => null,
    'disabled' => false,
    'placeholder' => null,
    'options' => [],
    
    // Error props
    'render_error' => true,
    'error_messages' => null,
    'error_class' => 'mt-2',
])

@php
    // Get error messages
    $errors = $error_messages ?? ($name ? $errors->get($name) : []);
    $hasErrors = !empty($errors) && $render_error;
@endphp

<div>
    {{-- Label --}}
    @if($label && $render_label)
        <label 
            @if($for) for="{{ $for }}" @endif
            class="block font-medium text-sm sm:text-base capitalize text-gray-700"
        >
            {{ $label }}
        </label>
    @endif
    
    {{-- Select Box --}}
    <div class="relative mb-2 mt-2">
        <select
            @if($name) name="{{ $name }}" @endif
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge(['class' => 'px-2 text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center  text-sm border-gray-400 rounded-sm border' ])!!}>
            <option value="">{{ ($placeholder == "" || $placeholder == null) ? __('messages.global.choose') : $placeholder }}</option>
            @foreach($options as $option)
                <option value="{{$option["value"]}}" @if(isset($option["selected"]) && $option["selected"]) selected @endif>{{$option["text"]}}</option>
            @endforeach
        </select>
    </div>
    
    {{-- Error Messages --}}
    @if($hasErrors)
        <ul class="text-sm text-red-600 space-y-1 {{ $error_class }}">
            @foreach ((array) $errors as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    @endif
</div>

