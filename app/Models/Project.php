<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'name',
        'address',
        'total_invoice',
        'total_paid',
        'total_debt',
        'end_date'
    ];
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
