@php
    $dropdownId = Str::slug($title);
    $linkClasses = ($active ?? false)
                    ? 'nav-link active py-2 px-4 rounded-smflex items-center bg-primary'
                    : 'nav-link py-2 px-4 hover:bg-gray-700 rounded-smflex items-center';
@endphp

<li class="mb-2" x-data="{ open: @js($active) }">
    <x-nav-link href="#" @click="open = !open"  :active="$active" :sub="false" data-dropdown-target="dropdown-{{ $dropdownId }}">
        
        @isset($icon) {{ $icon }} @endisset
        @isset($titleUi){{ $titleUi }}@endisset
        <i class="fas fa-chevron-down arrow-icon collapse-link-icon transition duration-300 ms-auto" :class="open ? 'rotate-0' : 'ltr:rotate-90 rtl:-rotate-90'"></i>
        
    
    </x-nav-link>
    @if(!empty($links))
        <ul x-show="open" class=" border-t border-gray-600 pt-2 pb-4" @click.outside="open = false" @close.stop="open = false" data-dropdown="dropdown-{{ $dropdownId }}" class="sub-menu mt-1 space-y-1">
            @foreach($links as $link)
            @if($link['render'] ?? true)
                <li>
                    @php 
                        $link_icon = $link['icon'] ?? 'far fa-circle';
                        $link_active = $link['active'] ?? false;
                        $link_sub = $link['subnav'] ?? false;
                        $link_classes = $link_active
                            ? ($link_sub
                                ? 'py-2 px-4 rounded-sm flex items-center bg-slate-100 text-slate-900 ms-2'
                                : 'py-2 px-4 rounded-sm flex items-center bg-blue-500 ms-2')
                            : 'py-2 px-4 hover:bg-gray-700 rounded-sm flex items-center ms-2';
                    @endphp
                    <a href="{{ $link['url'] }}" class="{{ $link_classes }}">
                        <i class="{{ $link_icon . ' nav-icon me-2 text-lg' }}"></i> {{ $link['title'] }}
                    </a>
                </li>
            @endif
            @endforeach
        </ul>
    @endif
</li>

