<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JavaDev - @yield('title', 'Home')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logos/logo-for-tab.png') }}">

    {{-- Satoshi Font dari Fontshare CDN --}}
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    {{-- Single Vite Entry Point --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F5F7FB] text-slate-900 antialiased">

    {{-- Flash Data for JS --}}
    @if(session('success') || session('error') || session('info'))
        <div id="flash-data" class="hidden" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
            data-info="{{ session('info') }}">
        </div>
    @endif

    {{-- NAVBAR --}}
    @include('partials.layout.navbar')

    {{-- Logout Toast (small, bottom-right, auto-dismiss 2s) --}}
    @if(session('logout_success'))
        <div id="logout-notification"
            class="fixed bottom-5 right-5 bg-slate-800 text-white px-4 py-2.5 rounded-lg shadow-lg z-50 flex items-center gap-2"
            style="transform: translateX(100%);">
            <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium">{{ session('logout_success') }}</span>
        </div>
        <script>
            (function () {
                const el = document.getElementById('logout-notification');
                if (!el) return;
                // Slide in
                requestAnimationFrame(() => {
                    el.style.transition = 'transform 0.3s ease-out';
                    el.style.transform = 'translateX(0)';
                });
                // Auto-dismiss after 2s
                setTimeout(() => {
                    el.style.transition = 'transform 0.3s ease-in, opacity 0.3s ease-in';
                    el.style.transform = 'translateX(100%)';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 300);
                }, 2000);
            })();
        </script>
    @endif

    {{-- Spacer for fixed navbar --}}
    <div class="h-16"></div>

    {{-- CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @include('partials.layout.footer')

</body>

</html>