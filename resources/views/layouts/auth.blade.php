<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PoSFigure')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="text-3xl font-black text-sky-700">
                PoS<span class="text-yellow-400">Figure</span>
            </a>
            <p class="text-gray-500 text-sm mt-1">@yield('subtitle', '')</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            @include('partials.flash')
            @yield('content')
        </div>
    </div>

</body>
</html>
