<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PoSFigure') — ร้านค้าฟิกเกอร์ โมเดล ของสะสม</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col">

    <header class="bg-sky-600 text-white shadow-md sticky top-0 z-40">
        <div class="max-w-[1600px] mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-2xl font-black tracking-tight">
                PoS<span class="text-yellow-300">Figure</span>
            </a>

            <form action="{{ route('products.search') }}" method="GET" class="hidden md:flex flex-1 max-w-md bg-white/10 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-white/40">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาฟิกเกอร์ โมเดล ..."
                       class="w-full bg-transparent px-4 py-2 text-sm text-white placeholder-white/70 focus:outline-none">
                <button class="bg-sky-800 hover:bg-sky-900 text-white font-semibold px-4 text-sm transition">
                    ค้นหา
                </button>
            </form>

            <nav class="flex items-center gap-3 text-sm font-medium">
                <a href="{{ route('products.index') }}" class="hover:text-yellow-300 transition hidden sm:block">สินค้า</a>
                <a href="{{ route('pages.howtoorder') }}" class="hover:text-yellow-300 transition hidden lg:block">วิธีสั่งซื้อ</a>
                <a href="{{ route('pages.contact') }}" class="hover:text-yellow-300 transition hidden lg:block">ติดต่อเรา</a>

                <a href="{{ route('cart.index') }}" class="relative flex items-center gap-1 bg-sky-700 hover:bg-sky-800 rounded-lg px-3 py-2 transition">
                    <span>🛒</span>
                    <span class="hidden sm:inline">ตะกร้า</span>
                    @if(app(\App\Services\CartService::class)->count() > 0)
                        <span class="absolute -top-2 -right-2 bg-yellow-400 text-sky-900 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                            {{ app(\App\Services\CartService::class)->count() }}
                        </span>
                    @endif
                </a>

                @auth
                    <a href="{{ route('member.dashboard') }}" class="hover:text-yellow-300 transition">{{ Auth::user()->name }}</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="hover:text-yellow-300 transition">ออกจากระบบ</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-yellow-300 transition">เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="bg-white text-sky-700 hover:bg-sky-50 font-bold rounded-lg px-3 py-2 transition">สมัคร</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="max-w-[1600px] w-full mx-auto px-4 py-6 flex-1">
        @include('partials.flash')
        @yield('content')
    </main>

    <footer class="bg-sky-900 text-sky-100 mt-auto">
        <div class="max-w-[1600px] mx-auto px-4 py-8 grid gap-8 md:grid-cols-3 text-sm">
            <div>
                <h3 class="text-white font-bold text-lg mb-2">PoSFigure</h3>
                <p>ร้านค้าฟิกเกอร์ โมเดล และของสะสม ของแท้จากญี่ปุ่น มีระบบพรี-ออเดอร์กับมัดจำสินค้า เพื่อให้ของถึงมือคุณแน่นอน 100%</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-2">เมนู</h4>
                <ul class="space-y-1">
                    <li><a href="{{ route('products.index') }}" class="hover:text-yellow-300">สินค้าทั้งหมด</a></li>
                    <li><a href="{{ route('pages.howtoorder') }}" class="hover:text-yellow-300">วิธีการสั่งซื้อ / จัดส่ง</a></li>
                    <li><a href="{{ route('pages.contact') }}" class="hover:text-yellow-300">ติดต่อเรา</a></li>
                    @auth
                        <li><a href="{{ route('member.dashboard') }}" class="hover:text-yellow-300">บัญชีของฉัน</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-2">ติดต่อ</h4>
                <p>Email: admin@posfigure.test</p>
                <p>Facebook: facebook.com/posfigure</p>
                <p>Line: @posfigure</p>
            </div>
        </div>
        <div class="border-t border-sky-800 py-4 text-center text-xs text-sky-300">
            &copy; {{ date('Y') }} PoSFigure — ITMI1201 Project
        </div>
    </footer>

</body>
</html>
