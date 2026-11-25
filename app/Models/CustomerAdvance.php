<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAdvance extends Model
{
    protected $fillable = ['payment_date', 'amount', 'payment_method', 'note', 'reference_code'];
    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];
}
