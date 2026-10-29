<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductReportController extends Controller
{
    /** สมาชิกแจ้งรายงานปัญหาสินค้า */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $allowed = array_keys(ProductReport::REASON_LABELS);

        $data = $request->validate([
            'reasons' => ['required', 'array', 'min:1'],
            'reasons.*' => ['required', 'string', 'in:'.implode(',', $allowed)],
            'other_detail' => [
                Rule::requiredIf(fn () => in_array(ProductReport::REASON_OTHER, (array) $request->input('reasons', []), true)),
                'nullable',
                'string',
                'max:1000',
            ],
            'detail' => ['nullable', 'string', 'max:2000'],
        ], [
            'other_detail.required' => 'กรุณาระบุข้อผิดพลาดอื่น ๆ ที่พบ',
        ]);

        $reasons = array_values(array_unique($data['reasons']));

        // กันสมาชิกรายงานสินค้าเดิมซ้ำจนกว่าจะได้รับการจัดการ
        $exists = ProductReport::where('product_id', $product->id)
            ->where('member_id', Auth::guard('web')->id())
            ->where('status', ProductReport::STATUS_PENDING)
            ->exists();

        if ($exists) {
            return back()->with('error', 'คุณได้รายงานสินค้านี้อยู่แล้ว ทางร้านกำลังตรวจสอบ');
        }

        ProductReport::create([
            'product_id' => $product->id,
            'member_id' => Auth::guard('web')->id(),
            'reasons' => $reasons,
            'other_detail' => in_array(ProductReport::REASON_OTHER, $reasons, true) ? ($data['other_detail'] ?? null) : null,
            'detail' => $data['detail'] ?? null,
            'status' => ProductReport::STATUS_PENDING,
        ]);

        return back()->with('success', 'ขอบคุณสำหรับการแจ้งรายงาน ทางร้านจะตรวจสอบสินค้าให้เร็ววัน');
    }

    // ===================== Admin =====================

    public function index(Request $request): View
    {
        $reports = ProductReport::with(['product', 'member', 'employee'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('reason'), fn ($q) => $q->whereJsonContains('reasons', $request->string('reason')))
            ->when($request->filled('q'), fn ($q) => $q->whereHas('product', fn ($p) => $p
                ->where('name', 'like', "%{$request->get('q')}%")
                ->orWhere('code', 'like', "%{$request->get('q')}%")
            ))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock.admin.reports', compact('reports'));
    }

    public function update(Request $request, ProductReport $report): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(ProductReport::STATUS_LABELS))],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $report->update([
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
            'handled_by' => Auth::guard('admin')->id(),
            'handled_at' => now(),
        ]);

        return back()->with('success', 'อัปเดตรายงานเรียบร้อยแล้ว');
    }
}
