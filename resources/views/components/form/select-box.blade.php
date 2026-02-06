@props(['disabled' => false, "placeholder" => null,"options"=>[]])


<div class="relative mb-2 mt-2">
    <select
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'px-2 text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center  text-sm border-gray-400 rounded-sm border' ])!!}>
        <option value="" >{{ ($placeholder == "" || $placeholder == null) ? __('messages.global.choose') : $placeholder }} </option>
        @foreach($options as $option)
            <option value="{{$option["value"]}}" @if(isset($option["selected"]) && $option["selected"]) selected @endif>{{$option["text"]}}</option>
        @endforeach
    </select>
</div>
