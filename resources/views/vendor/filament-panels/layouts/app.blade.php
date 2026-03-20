<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $faviconPath = \App\Models\SiteSetting::get('site_favicon');
        $faviconUrl  = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.svg');
        $faviconMime = str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png';
        $touchPath   = \App\Models\SiteSetting::get('site_apple_touch_icon');
        $touchUrl    = $touchPath ? asset('storage/' . $touchPath) : $faviconUrl;
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="{{ $faviconMime }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $touchUrl }}">
    <title>{{ config('app.name', 'Admin') }}</title>
    @livewireStyles
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="filament-body">
    {{ $slot }}
    @livewireScripts
    @stack('scripts')
</body>
</html>
