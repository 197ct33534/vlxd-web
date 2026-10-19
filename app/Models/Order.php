<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'draft',
        'pending_confirm',
        'confirmed',
        'delivering',
        'partially_delivered',
        'delivered',
        'cancelled',
    ];

    protected $fillable = [
        'code',
        'customer_id',
        'project_id',
        'invoice_id',
        'delivery_address',
        'status',
        'total_amount',
        'note',
        'confirmed_by',
        'confirmed_at',
        'driver_id',
        'delivering_at',
        'delivered_at',
        'delivery_photo',
        'delivery_lat',
        'delivery_lng',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'delivering_at' => 'datetime',
        'delivered_at' => 'datetime',
        'delivery_lat' => 'decimal:7',
        'delivery_lng' => 'decimal:7',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function canExportInvoice(): bool
    {
        return $this->invoice_id === null
            && $this->project_id !== null
            && in_array($this->status, ['confirmed', 'delivering', 'partially_delivered', 'delivered'], true);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Nháp',
            'pending_confirm' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'delivering' => 'Đang giao',
            'partially_delivered' => 'Giao một phần',
            'delivered' => 'Đã giao đủ',
            'cancelled' => 'Đã hủy',
            default => $this->status,
        };
    }

    public static function generateCode(): string
    {
        return 'DH' . now()->format('ymd') . str_pad((string) (static::whereDate('created_at', today())->count() + 1), 3, '0', STR_PAD_LEFT);
    }

    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->save();
    }
}
