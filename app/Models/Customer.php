<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'address', 'phone', 'email', 'status', 'credit_limit', 'credit_days'];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'credit_days' => 'integer',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Project::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
