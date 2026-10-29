<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY_TO_SHIP = 'ready_to_ship';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        'pending_payment' => 'รอชำระเงิน',
        'paid' => 'ชำระเงินแล้ว',
        'processing' => 'กำลังเตรียมสินค้า',
        'ready_to_ship' => 'พร้อมจัดส่ง',
        'completed' => 'สำเร็จ',
        'cancelled' => 'ยกเลิก',
    ];

    protected $fillable = [
        'order_no',
        'member_id',
        'status',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'shipping_method',
        'shipping_fee',
        'subtotal',
        'deposit_total',
        'balance_due_date',
        'balance_reminder_sent_at',
        'total',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'balance_due_date' => 'date',
            'balance_reminder_sent_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function paidAmount(): int
    {
        return (int) $this->payments->where('status', 'verified')->sum('amount');
    }

    public function dueAmount(): int
    {
        return $this->total - $this->paidAmount();
    }

    public function amountDueNow(): int
    {
        if ($this->payFirstPayment()) {
            return $this->initialPaymentAmount();
        }

        return $this->dueAmount();
    }

    public function payFirstPayment(): bool
    {
        return $this->paidAmount() === 0 && $this->isPreorder();
    }

    public function initialPaymentAmount(): int
    {
        if (! $this->isPreorder()) {
            return $this->total;
        }

        if ($this->items->isEmpty()) {
            return min($this->deposit_total, $this->total);
        }

        $amount = 0;

        foreach ($this->items as $item) {
            $unitAmount = (int) $item->deposit > 0 ? (int) $item->deposit : (int) $item->price;
            $amount += $unitAmount * (int) $item->qty;
        }

        return min($amount, $this->total);
    }

    public function hasInStockItems(): bool
    {
        return $this->items->contains(fn ($item) => (int) $item->deposit === 0);
    }

    public function isPreorder(): bool
    {
        return $this->deposit_total > 0;
    }

    public function hasOutstandingBalance(): bool
    {
        return $this->dueAmount() > 0;
    }

    public function isBalanceDue(): bool
    {
        return $this->balance_due_date !== null
            && $this->balance_due_date->lte(today())
            && $this->hasOutstandingBalance()
            && in_array($this->status, [
                self::STATUS_PAID,
                self::STATUS_PROCESSING,
                self::STATUS_READY_TO_SHIP,
            ], true);
    }

    public function isBalanceUpcoming(): bool
    {
        return $this->balance_due_date !== null
            && $this->balance_due_date->isFuture()
            && $this->hasOutstandingBalance()
            && in_array($this->status, [
                self::STATUS_PAID,
                self::STATUS_PROCESSING,
                self::STATUS_READY_TO_SHIP,
            ], true);
    }
}