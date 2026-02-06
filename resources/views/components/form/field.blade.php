@props([
    // Label props
    'label' => null,
    'for' => null,
    'render_label' => true,
    
    // Input/Textarea props
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'icon' => false,
    'input_icon' => null,
    'disabled' => false,
    
    // Error props
    'render_error' => true,
    'error_messages' => null,
    'error_class' => 'mt-2',
    
    // Field type: 'input' or 'textarea'
    'field_type' => 'input',
])

@php
    // Determine if this is a textarea or input
    $isTextarea = $field_type === 'textarea';
    
    // Get error messages
    $errors = $error_messages ?? ($name ? $errors->get($name) : []);
    $hasErrors = !empty($errors) && $render_error;
    
    // Input classes
    $inputClasses = $icon || $input_icon
        ? 'ps-16'
        : ($isTextarea ? 'px-2 py-1' : 'ps-4');
    
    // Input base classes
    $inputBaseClasses = $isTextarea
        ? 'px-2 py-1 text-sm sm:text-base text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-auto flex items-center border-gray-400 rounded-sm border'
        : 'text-base text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center border border-gray-400 rounded-sm ' . $inputClasses;
@endphp

<div>
    {{-- Input Label --}}
    @if($label && $render_label)
        <label 
            @if($for) for="{{ $for }}" @endif
            class="block font-medium text-sm sm:text-base capitalize text-gray-700"
        >
            {{ $label }}
        </label>
    @endif
    
    {{-- Text Input or Textarea --}}
    <div class="relative mb-2 mt-2 {{ $isTextarea ? 'text-sm sm:text-base' : 'text-base' }}">
        @if($input_icon)
            <div class="absolute text-gray-600 flex items-center px-4 border-e border-gray-400 h-full">
                {{ $input_icon }}
            </div>
        @endif
        
        @if($isTextarea)
            <textarea
                @if($name) name="{{ $name }}" @endif
                @if($disabled) disabled @endif
                {!! $attributes->merge(['class' => $inputBaseClasses]) !!}
            >{{ trim($value ?? $slot ?? '') }}</textarea>
        @else
            <input
                @if($name) name="{{ $name }}" @endif
                @if($type) type="{{ $type }}" @endif
                @if($value !== null) value="{{ $value }}" @endif
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                @if($disabled) disabled @endif
                {!! $attributes->merge(['class' => $inputBaseClasses]) !!}
            />
        @endif
    </div>
    
    {{-- Input Error --}}
    @if($hasErrors)
        <ul class="text-sm text-red-600 space-y-1 {{ $error_class }}">
            @foreach ((array) $errors as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    @endif
</div>

