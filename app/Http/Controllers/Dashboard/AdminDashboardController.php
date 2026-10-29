<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();

        $stats = [
            'todayRevenue' => Order::whereDate('created_at', $today)
                ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP, Order::STATUS_COMPLETED])
                ->sum('total'),
            'totalOrders' => Order::count(),
            'pendingPayments' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'activeProducts' => Product::published()->count(),
            'totalMembers' => Member::count(),
            'newMembersToday' => Member::whereDate('created_at', $today)->count(),
            'balanceDueCount' => Order::whereNotNull('balance_due_date')
                ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP])
                ->whereDate('balance_due_date', '<=', today())
                ->get()
                ->filter(fn (Order $order) => $order->dueAmount() > 0)
                ->count(),
            'balanceUpcomingCount' => Order::whereNotNull('balance_due_date')
                ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP])
                ->whereDate('balance_due_date', '>', today())
                ->get()
                ->filter(fn (Order $order) => $order->dueAmount() > 0)
                ->count(),
        ];

        $recentOrders = Order::with(['member', 'items'])->latest()->take(8)->get();
        $pendingPayments = Payment::with(['order', 'member'])->where('status', Payment::STATUS_PENDING)->latest()->take(8)->get();
        $balanceDueOrders = Order::with(['member'])
            ->whereNotNull('balance_due_date')
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP])
            ->whereDate('balance_due_date', '<=', today())
            ->orderBy('balance_due_date')
            ->get()
            ->filter(fn (Order $order) => $order->dueAmount() > 0)
            ->take(6);
        $topProducts = Product::withCount(['orderItems as sold_qty' => fn ($q) => $q->selectRaw('sum(qty)')])
            ->orderByDesc('sold_qty')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentOrders', 'pendingPayments', 'topProducts', 'balanceDueOrders'));
    }
}