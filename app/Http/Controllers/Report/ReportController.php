<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now();

        $query = Order::whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP, Order::STATUS_COMPLETED])
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);

        $summary = [
            'orders' => (clone $query)->count(),
            'revenue' => (clone $query)->sum('subtotal'),
            'deposits' => (clone $query)->sum('deposit_total'),
            'shipping' => (clone $query)->sum('shipping_fee'),
            'items' => (clone $query)->withSum('items as total_qty', 'qty')->get()->sum('total_qty'),
        ];

        $byDay = (clone $query)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as orders, SUM(subtotal) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topProducts = Product::whereHas('orderItems', function ($q) use ($from, $to) {
            $q->whereHas('order', fn ($o) => $o->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP, Order::STATUS_COMPLETED]));
        })
            ->withCount(['orderItems as sold_qty' => fn ($q) => $q->selectRaw('sum(qty)')])
            ->orderByDesc('sold_qty')
            ->take(10)
            ->get();

        $byCategory = Category::withCount(['products as sold_qty' => function ($q) use ($from, $to) {
            $q->whereHas('orderItems', fn ($oi) => $oi->whereHas('order', fn ($o) => $o->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP, Order::STATUS_COMPLETED])));
        }])
            ->having('sold_qty', '>', 0)
            ->get();

        return view('report.sales', compact('from', 'to', 'summary', 'byDay', 'topProducts', 'byCategory'));
    }
}