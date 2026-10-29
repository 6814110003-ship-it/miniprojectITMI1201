<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>แจ้งเตือนชำระยอดคงเหลือ</title>
</head>
<body style="font-family: Tahoma, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="color: #0369a1;">PoSFigure — แจ้งเตือนชำระยอดคงเหลือ</h2>

    <p>สวัสดี {{ $order->member?->name ?? $order->customer_name }},</p>

    <p>
        ออเดอร์ <strong>{{ $order->order_no }}</strong> ถึงกำหนดชำระยอดคงเหลือแล้ว
        กรุณาชำระเงินเพื่อให้ร้านดำเนินการจัดส่ง
    </p>

    <table cellpadding="6" style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="color: #6b7280;">ยอดรวมออเดอร์</td>
            <td style="text-align: right;">฿{{ number_format($order->total) }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">ชำระแล้ว</td>
            <td style="text-align: right; color: #16a34a;">฿{{ number_format($order->paidAmount()) }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">ยอดคงเหลือ</td>
            <td style="text-align: right; color: #dc2626; font-weight: bold;">฿{{ number_format($order->dueAmount()) }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">วันนัดเก็บ</td>
            <td style="text-align: right;">{{ $order->balance_due_date?->format('d/m/Y') }}</td>
        </tr>
    </table>

    <p>
        <a href="{{ route('member.orders.show', $order) }}"
           style="display: inline-block; background: #0284c7; color: white; padding: 10px 18px; border-radius: 8px; text-decoration: none;">
            ดูออเดอร์และแจ้งชำระเงิน
        </a>
    </p>

    <p style="font-size: 12px; color: #9ca3af;">
        อีเมลนี้ส่งจากระบบ PoSFigure อัตโนมัติ กรุณาอย่าตอบกลับ
    </p>
</body>
</html>
