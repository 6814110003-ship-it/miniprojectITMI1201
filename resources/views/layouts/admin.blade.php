<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin PoSFigure</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">

    <div class="flex min-h-screen">
        <aside class="w-60 bg-gray-900 text-gray-300 shrink-0 shadow-lg hidden md:block">
            <div class="px-5 py-4 border-b border-gray-700">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-black text-white">
                    PoS<span class="text-yellow-400">Figure</span>
                </a>
                <p class="text-xs text-gray-400 mt-1">ระบบหลังบ้าน</p>
            </div>
            <nav class="py-4 text-sm font-medium space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800 @yield('nav-active', '')">📊 Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">📦 สินค้า</a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">🗂️ หมวดหมู่</a>
                <a href="{{ route('admin.tags.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800"># แท็ก</a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">🧾 ออเดอร์</a>
                <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">💳 ชำระเงิน</a>
                <a href="{{ route('admin.reports.sales') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">📈 รายงาน</a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">🚩 รายงานสินค้า</a>
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">⭐ รีวิวสินค้า</a>
                <a href="{{ route('admin.members.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">👤 ลูกค้า</a>
                @if(Auth::guard('admin')->user()?->is_admin)
                    <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-2 px-5 py-2 hover:bg-gray-800">👥 พนักงาน</a>
                @endif
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white shadow-sm border-b px-6 py-3 flex items-center justify-between">
                <h1 class="font-bold text-lg truncate">@yield('title', 'Dashboard')</h1>
                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('home') }}" class="text-blue-600 hover:underline" target="_blank">ดูหน้าร้าน ↗</a>
                    <span class="text-gray-500">{{ Auth::guard('admin')->user()->name }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="bg-sky-600 hover:bg-sky-700 text-white rounded-lg px-3 py-1.5 text-xs font-bold">ออกจากระบบ</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-6">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
