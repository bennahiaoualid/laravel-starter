<div x-data="{ {{ $model }}: '{{ $value }}' }" class="mt-6">
    <input type="hidden" name="{{ $name }}" x-model="{{ $model }}">
    
    <div class="mt-2 grid {{ $grid }} {{ $gap }}">
        {{ $slot }}
    </div>
    
    @error($name)
        <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div> 