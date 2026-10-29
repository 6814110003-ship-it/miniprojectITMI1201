<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReport extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_DISMISSED = 'dismissed';

    public const STATUS_LABELS = [
        'pending' => 'รอตรวจสอบ',
        'resolved' => 'แก้ไขแล้ว',
        'dismissed' => 'ไม่พบปัญหา',
    ];

    public const REASON_INFO = 'wrong_info';
    public const REASON_IMAGE = 'wrong_image';
    public const REASON_PRICE = 'overprice';
    public const REASON_OTHER = 'other';

    public const REASON_LABELS = [
        self::REASON_INFO => 'ข้อมูลสินค้าไม่ถูกต้อง',
        self::REASON_IMAGE => 'รูปภาพไม่เหมาะสม / ไม่ตรงสินค้า',
        self::REASON_PRICE => 'ราคาไม่ถูกต้อง / สูงเกินจริง',
        self::REASON_OTHER => 'อื่น ๆ',
    ];

    protected $fillable = [
        'product_id',
        'member_id',
        'reasons',
        'other_detail',
        'detail',
        'status',
        'handled_by',
        'admin_note',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'reasons' => 'array',
            'handled_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'handled_by');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /** @return array<int, string> รายการเหตุผลที่ถูกเลือก */
    public function reasonList(): array
    {
        $reasons = is_array($this->reasons) ? $this->reasons : [];

        return array_values(array_filter(
            array_map(fn ($key) => self::REASON_LABELS[$key] ?? null, $reasons)
        ));
    }

    public function reasonLabel(): string
    {
        $list = $this->reasonList();

        return $list ? implode(', ', $list) : '-';
    }
}
