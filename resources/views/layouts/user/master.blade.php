<!DOCTYPE html>
@if(app()->isLocale('ar'))
<html lang="ar" dir="rtl" class="font-arabic">
@else
<html lang="{{ app()->getLocale() }}" dir="ltr">
@endif

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title --}}
    <title>@yield('title', config('app.name'))</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

    {{-- Project Frontend (ALL compiled assets through Vite) --}}
    @vite([
        'resources/css/app.css',
        'resources/css/sidebar.css',
        'resources/js/app.js',
        'resources/js/sidebar.js'
    ])

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Page-level CSS --}}
    @stack('css')
</head>

<body class="min-h-screen flex">

    <div class="wrapper flex flex-1">

        {{-- Sidebar --}}
        @include('layouts.user.sidebar')

        <div class="flex-1 flex flex-col">

            {{-- Header --}}
            @include('layouts.user.main-header')

            {{-- Content --}}
            <main class="flex-1 p-2 md:p-4">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Global modals --}}
    <x-notification-detail-modal />

    {{-- Livewire Scripts --}}
    @livewireScripts

    @include('layouts.session_notifications_taoster')

    {{-- Page-level scripts --}}
    @stack('scripts')

</body>
</html>
