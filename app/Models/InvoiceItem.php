<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'date',
        'product_name',
        'unit',
        'quantity',
        'price',
        'amount'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function deliveryDate(string $format = 'd/m/Y'): ?string
    {
        if (!$this->date) {
            return null;
        }

        $date = str_contains($this->date, '/')
            ? Carbon::createFromFormat('!d/m/Y', $this->date)
            : Carbon::parse($this->date);

        return $date->format($format);
    }
}
