<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JavaDev - @yield('title', 'Auth')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logos/logo-for-tab.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('scripts')
</head>

<body class="@yield('body-class', 'auth-body')">
    {{-- Flash Data for JS --}}
    @if(session('success') || session('error') || session('info'))
        <div id="flash-data" class="hidden"
            data-success="{{ session('success') }}"
            data-error="{{ session('error') }}"
            data-info="{{ session('info') }}">
        </div>
    @endif

    @hasSection('full-layout')
        @yield('content')
    @else
        <main class="auth-wrap">
            @yield('content')
        </main>
    @endif
</body>

</html>