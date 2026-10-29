<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public const STATUS_PREORDER = 'preorder';
    public const STATUS_INSTOCK = 'instock';
    public const STATUS_SOLD_OUT = 'soldout';

    public const STATUS_LABELS = [
        'preorder' => 'พรี-ออเดอร์',
        'instock' => 'มีของ / เข้าร้าน',
        'soldout' => 'หมด / ปิดรับจอง',
    ];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'code',
        'description',
        'price',
        'deposit',
        'status',
        'size',
        'release_date',
        'preorder_deadline',
        'is_new',
        'is_featured',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'preorder_deadline' => 'date',
            'is_new' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ProductReport::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('status', ProductReview::STATUS_APPROVED);
    }

    public function coverImage(): ?string
    {
        $image = $this->images->first();

        return $image ? $image->path : null;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isPreorder(): bool
    {
        return $this->status === self::STATUS_PREORDER;
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        $query->where(fn ($q) => $q
            ->where('name', 'like', "%{$term}%")
            ->orWhere('code', 'like', "%{$term}%")
            ->orWhereHas('tags', fn ($t) => $t->where('name', 'like', "%{$term}%")));

        return $query;
    }
}