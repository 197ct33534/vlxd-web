<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnitConversion extends Model
{
    protected $fillable = [
        'product_id',
        'unit_name',
        'conversion_factor',
        'is_default',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:4',
        'is_default' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
