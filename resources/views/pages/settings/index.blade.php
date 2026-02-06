@extends('layouts.user.master')

@section('title')
    {{__('settings.title')}}
@endsection

@section('page_title')
    {{ __('settings.title') }}
@endsection

@section('content')
    @php
        $text_color = 'text-gray-700';
        $bg_color = 'bg-gray-600';
        $border_color = 'border-gray-600';
    @endphp
    <div class="flex justify-between items-center my-2 p-4 shadow-sm">
        <div>
            <h1 class="text-xl font-bold">{{__('settings.title')}}</h1>
            <p class="text-gray-600">{{__('settings.subtitle')}}</p>
        </div>
        <div class="flex space-x-2">
            <form method="POST" action="{{ route('settings.refresh-cache') }}" class="inline">
                @csrf
                <x-button type="submit" color_type="secondary">
                    <i class="fas fa-sync-alt me-2"></i>
                    {{__('settings.common.refresh')}}
                </x-button>
            </form>
        </div>
    </div>

    @foreach($data as $category => $settings)
    <div class="mb-8 border-t-4 {{ $border_color }} pt-4 rounded-md" x-data="{ open: true }">
        <div 
            class="flex items-center mb-4 p-4 gap-2 cursor-pointer hover:bg-gray-50 rounded-lg transition-colors"
            @click="open = !open"
        >
            <div class="p-2 {{ $bg_color }} rounded-lg">
                <i class="fas fa-building text-white text-xl"></i>
            </div>
            <div class="ml-3 flex-1 space-y-2">
                <h2 class="text-xl font-semibold text-gray-900">{{__("settings.categories.{$category}.title")}}</h2>
                <p class="text-sm text-gray-600">{{ __("settings.categories.{$category}.description") }}</p>
            </div>
            <div class="flex items-center">
                <i class="fas fa-chevron-up text-gray-500 transition-transform duration-300" :class="open ? 'rotate-0' : 'rotate-180'"></i>
            </div>
        </div>
        
        <div 
            class="grid grid-cols-1 lg:grid-cols-2 gap-6"
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            @foreach($settings as $setting)
                @if($setting->setting_key === 'company_logo')
                    {{-- Logo Upload Section --}}
                    <div class="bg-white rounded-lg shadow-md p-2 md:p-6 border border-gray-200">
                        <div class="flex items-center mb-4 gap-2">
                            <div class="p-2 {{ $bg_color }} rounded-lg">
                                <i class="fas fa-image text-white text-xl"></i>
                            </div>
                            <div class="ml-3 flex-1 space-y-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ __("{$setting->setting_trans_key}.name") }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    {{ __("{$setting->setting_trans_key}.description") }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            {{-- Current Logo Preview --}}
                            @php
                                $logoUrl = $setting->setting_value ? \Illuminate\Support\Facades\Storage::disk('public')->url($setting->setting_value) : null;
                            @endphp
                            
                            @if($logoUrl)
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-700">{{__('settings.common.current_value')}}:</span>
                                        <span class="text-xs text-gray-500">{{ $setting->setting_value }}</span>
                                    </div>
                                    <div class="flex justify-center p-4 bg-gray-50 rounded-lg">
                                        <img src="{{ $logoUrl }}" alt="Company Logo" class="max-w-full h-auto max-h-48 rounded-lg shadow-sm">
                                    </div>
                                    <form method="POST" action="{{ route('settings.delete-logo') }}" class="inline">
                                        @csrf
                                        <x-button type="submit" color_type="danger" class="w-full">
                                            <x-slot:icon>
                                                <i class="fas fa-trash me-2"></i>
                                            </x-slot:icon>
                                            {{__('settings.common.delete')}} Logo
                                        </x-button>
                                    </form>
                                </div>
                            @else
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">{{__('settings.common.current_value')}}:</span>
                                    <span class="text-sm text-gray-500 italic">No logo uploaded</span>
                                </div>
                            @endif

                            {{-- Upload Form --}}
                            <form method="POST" action="{{ route('settings.update', [$category, $setting->setting_key]) }}" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-input-label for="logo" :value="__('settings.common.new_value')" />
                                    <input 
                                        id="logo" 
                                        type="file" 
                                        class="mt-1 block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100" 
                                        name="logo"
                                        accept="image/png,image/jpeg,image/jpg"
                                        />
                                    <p class="mt-1 text-xs text-gray-500">{{ __("{$setting->setting_trans_key}.help") }}</p>
                                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                                </div>
                                
                                <div class="flex justify-end">
                                    <x-button type="submit" color_type="primary">
                                        <x-slot:icon>
                                            <i class="fas fa-upload me-2"></i>
                                        </x-slot:icon>
                                        {{__('settings.common.update')}}
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Regular Setting --}}
                    <div class="bg-white rounded-lg shadow-md p-2 md:p-6 border border-gray-200">
                        <div class="flex items-center mb-4 gap-2">
                            <div class="p-2 {{ $bg_color }} rounded-lg">
                                <i class="fas fa-id-badge text-white text-xl"></i>
                            </div>
                            <div class="ml-3 flex-1 space-y-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ __("{$setting->setting_trans_key}.name") }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    {{ __("{$setting->setting_trans_key}.description") }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{__('settings.common.current_value')}}:</span>
                                <span class="font-semibold text-lg text-gray-900">{{ $setting->setting_value }}</span>
                            </div>

                            <form method="POST" action="{{ route('settings.update', [$category, $setting->setting_key]) }}" class="space-y-3">
                                @csrf
                                @method('PUT')

                                <div>
                                    @if($setting->setting_key === 'invoice_default_language')
                                        @php
                                            $languageOptions = [
                                                ['value' => 'ar', 'text' => __('invoice.language_ar')],
                                                ['value' => 'en', 'text' => __('invoice.language_en')],
                                                ['value' => 'fr', 'text' => __('invoice.language_fr')],
                                            ];
                                        @endphp
                                        <x-form.select-box-field 
                                            name="value"
                                            :value="$setting->setting_value"
                                            :label=" ucwords(__('settings.common.new_value'))"
                                            for="value_{{ $setting->setting_key }}"
                                            :placeholder=" ucwords(__('settings.common.new_value'))"
                                            :options="$languageOptions"
                                            :error_messages="$errors->getBag($setting->setting_key)->all()"
                                        />
                                    @else
                                        <x-form.field 
                                            name="value"
                                            :value="$setting->setting_value"
                                            :label=" ucwords(__('settings.common.new_value'))"
                                            for="value_{{ $setting->setting_key }}"
                                            :placeholder=" ucwords(__('settings.common.new_value'))"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :error_messages="$errors->getBag($setting->setting_key)->all()"
                                        />
                                    @endif
                                </div>
                                
                                <div class="flex justify-end">
                                    <x-button type="submit" color_type="primary">
                                        <x-slot:icon>
                                            <i class="fas fa-save me-2"></i>
                                        </x-slot:icon>
                                        {{__('settings.common.update')}}
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endforeach


    @php
        $hasAnySettings = false;
        foreach($data as $settings) {
            if($settings->isNotEmpty()) {
                $hasAnySettings = true;
                break;
            }
        }
    @endphp

    @if(!$hasAnySettings)
        <div class="text-center py-12">
            <i class="fas fa-cogs text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No System Settings Found</h3>
            <p class="text-gray-600">System settings will appear here once they are configured.</p>
        </div>
    @endif
@endsection 