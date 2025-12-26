<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JavaDev - @yield('title', 'Error')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logos/logo-for-tab.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="error-body">
    <main>
        @yield('content')
    </main>
</body>

</html>
