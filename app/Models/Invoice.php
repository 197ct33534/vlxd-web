<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'code',
        'invoice_date',
        'total_amount',
        'total_text',
        'note',
        'vat_rate',
        'vat_amount',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function outstandingAmount(): float
    {
        $allocated = array_key_exists('payment_allocations_sum_amount', $this->attributes)
            ? $this->payment_allocations_sum_amount
            : $this->paymentAllocations()->sum('amount');

        return max(0, round((float) $this->total_amount - (float) $allocated, 2));
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
