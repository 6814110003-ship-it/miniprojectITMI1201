<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    public const STATUS_LABELS = [
        'pending' => 'รอตรวจสอบ',
        'verified' => 'อนุมัติแล้ว',
        'rejected' => 'ปฏิเสธ',
    ];

    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_BALANCE = 'balance';

    public const TYPE_LABELS = [
        'deposit' => 'มัดจำ',
        'balance' => 'ค่าสินค้า (ส่วนที่เหลือ)',
    ];

    protected $fillable = [
        'order_id',
        'member_id',
        'employee_id',
        'type',
        'method',
        'bank',
        'transferred_at',
        'amount',
        'slip_path',
        'ref_no',
        'note',
        'status',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'transferred_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}