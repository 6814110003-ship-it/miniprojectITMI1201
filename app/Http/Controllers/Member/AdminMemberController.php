<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Order;
use App\Models\ProductReport;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->get('q').'%';
                $query->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->withCount('orders')
            ->withSum(
                ['orders as total_spent' => fn ($q) => $q->where('status', '!=', Order::STATUS_CANCELLED)],
                'total'
            )
            ->withMax('orders as last_order_at', 'created_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalMembers = Member::count();
        $newThisMonth = Member::where('created_at', '>=', now()->startOfMonth())->count();

        return view('member.admin.index', compact('members', 'totalMembers', 'newThisMonth'));
    }

    public function show(Member $member): View
    {
        $stats = [
            'totalOrders' => $member->orders()->count(),
            'completedOrders' => $member->orders()->where('status', Order::STATUS_COMPLETED)->count(),
            'cancelledOrders' => $member->orders()->where('status', Order::STATUS_CANCELLED)->count(),
            'totalSpent' => $member->orders()->where('status', '!=', Order::STATUS_CANCELLED)->sum('total'),
            'reviews' => ProductReview::where('member_id', $member->id)->count(),
            'reports' => ProductReport::where('member_id', $member->id)->count(),
        ];

        $orders = $member->orders()
            ->withCount('items')->with('payments')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('member.admin.show', compact('member', 'stats', 'orders'));
    }
}
