@php
    $classes = ($active ?? false)
                ? (($sub ?? false)
                    ?'py-2 px-4 rounded-sm flex items-center bg-slate-100 text-slate-900'
                    :'py-2 px-4 rounded-sm flex items-center bg-primary')
                : 'py-2 px-4 hover:bg-gray-700 rounded-sm flex items-center';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @isset($icon) {{ $icon }} @endisset  {{ $slot }}
</a>
