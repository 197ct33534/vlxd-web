<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryNoteItem extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'order_item_id',
        'product_id',
        'product_name',
        'trade_unit',
        'quantity',
        'converted_quantity',
        'is_overridden',
        'unit_price',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'converted_quantity' => 'decimal:3',
        'is_overridden' => 'boolean',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
