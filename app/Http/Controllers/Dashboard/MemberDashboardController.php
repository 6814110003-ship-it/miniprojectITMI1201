<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberDashboardController extends Controller
{
    public function index(): View
    {
        $member = Auth::guard('web')->user();

        $stats = [
            'totalOrders' => $member->orders()->count(),
            'pendingOrders' => $member->orders()->where('status', 'pending_payment')->count(),
            'completedOrders' => $member->orders()->where('status', 'completed')->count(),
            'totalSpent' => $member->orders()->where('status', '!=', 'cancelled')->sum('subtotal'),
        ];

        $recentOrders = $member->orders()->with(['items', 'payments'])->latest()->take(5)->get();

        $balanceOrders = $member->orders()
            ->whereNotNull('balance_due_date')
            ->whereIn('status', [
                Order::STATUS_PAID,
                Order::STATUS_PROCESSING,
                Order::STATUS_READY_TO_SHIP,
            ])
            ->withSum(
                ['payments as verified_paid' => fn ($q) => $q->where('status', Payment::STATUS_VERIFIED)],
                'amount'
            )
            ->orderBy('balance_due_date')
            ->get()
            ->filter(fn (Order $order) => $order->total > (int) $order->verified_paid);

        $balanceDueOrders = $balanceOrders->filter(
            fn (Order $order) => $order->balance_due_date->lte(today())
        );

        $upcomingBalanceOrders = $balanceOrders->filter(
            fn (Order $order) => $order->balance_due_date->isFuture()
        )->take(3);

        return view('member.dashboard', compact(
            'member',
            'stats',
            'recentOrders',
            'balanceDueOrders',
            'upcomingBalanceOrders'
        ));
    }
}
