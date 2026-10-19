<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryNote extends Model
{
    public const STATUSES = [
        'pending',
        'delivering',
        'delivered',
        'cancelled',
    ];

    protected $fillable = [
        'code',
        'order_id',
        'project_id',
        'driver_id',
        'license_plate',
        'delivery_date',
        'status',
        'shipping_fee',
        'driver_collected_cash',
        'is_direct_delivery',
        'photo_url',
        'lat',
        'lng',
        'note',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'shipping_fee' => 'decimal:2',
        'driver_collected_cash' => 'boolean',
        'is_direct_delivery' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Chờ giao',
            'delivering' => 'Đang giao',
            'delivered' => 'Đã hạ hàng',
            'cancelled' => 'Đã hủy',
            default => $this->status,
        };
    }
}
