<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'code', 'phone', 'address', 'tax_code', 'note',
    ];

    public function receipts(): HasMany
    {
        return $this->hasMany(StockReceipt::class);
    }
}
