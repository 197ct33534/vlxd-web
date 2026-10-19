<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'base_unit',
        'retail_price',
        'is_active',
        'stock_quantity',
        'avg_cost',
    ];

    protected $casts = [
        'retail_price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'decimal:3',
        'avg_cost' => 'decimal:2',
    ];

    public function conversions(): HasMany
    {
        return $this->hasMany(ProductUnitConversion::class);
    }

    public function stockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class);
    }

    public function stockReceiptItems(): HasMany
    {
        return $this->hasMany(StockReceiptItem::class);
    }
}
